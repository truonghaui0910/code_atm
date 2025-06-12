/**
 * Custom Group Dropdown Plugin
 * A reusable dropdown plugin that can be used multiple times on a page
 * with customizable input IDs and dropdown triggers
 */
(function($) {
    // Plugin definition
    $.fn.groupDropdown = function(options) {
        
        if (options === 'refresh') {
            return this.each(function() {
                const $container = $(this);
                // Call the fetchGroups function stored in data
                const pluginData = $container.data('groupDropdown');
                if (pluginData && typeof pluginData.fetchGroups === 'function') {
                    pluginData.fetchGroups();
                    return true;
                }
                return false;
            });
        }
        
        // Default settings
        const settings = $.extend({
            inputSelector: '#group_channel_search', // ID of hidden input for selected value
            multipleSelect: false,                  // Allow multiple selection
            defaultSelected: -1,                    // Default selected value
            ajaxUrl: '/ajaxListGroupChannel',       // URL to fetch groups
            updateUrl: '/ajaxUpdateGroupChannel',   // URL to update groups
            deleteUrl: '/ajaxDelGroupChannel',      // URL to delete groups
            onSelect: function(selectedIds) {}      // Callback after selection
        }, options);

        // Each instance of the plugin
        return this.each(function() {
            const $container = $(this);
            const containerId = $container.attr('id');
            
            // Generate unique IDs for components
            const dropdownId = `dropdown_${containerId}`;
            const menuId = `menu_${containerId}`;
            const listId = `list_${containerId}`;
            const searchId = `search_${containerId}`;
            const inputId = settings.inputSelector;
            
            // Store state for this instance
            let mockData = [-1];
            let selectedGroups = [settings.defaultSelected];
            
            // Initialize HTML structure
            $container.html(`
                <button class="btn dropdown-toggle btn-dropdown-group" type="button" id="${dropdownId}">
                    Select Group
                </button>
                <div class="select-dropdown-menu" id="${menuId}">
                    <input type="text" id="${searchId}" class="search-box" placeholder="Search...">
                    <ul id="${listId}" class="sortable-list div_scroll_50"></ul>
                </div>
            `);
            
            // Cache jQuery elements
            const $dropdown = $(`#${dropdownId}`);
            const $menu = $(`#${menuId}`);
            const $list = $(`#${listId}`);
            const $search = $(`#${searchId}`);
            const $input = $(inputId);
            
            // Initialize with default value if provided
            if (settings.defaultSelected !== -1) {
                $input.val(settings.defaultSelected);
            }
            
            // Fetch groups from server
            function fetchGroups() {
//                console.log("Fetching groups from", settings.ajaxUrl);
                $.ajax({
                    url: settings.ajaxUrl,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
//                        console.log("Received response:", response);
                        mockData = [];
                        mockData = response.map(item => ({
                            id: item.id,
                            name: item.group_name,
                            username: item.user_name,
                            channels: item.channels
                        }));
//                        console.log("Updated mockData:", mockData);
                        loadGroups();
                    },
                    error: function() {
                        alert("Error loading group data!");
                    }
                });
            }
            
            // Render groups list
            function loadGroups() {
//                let nogr = selectedGroups.includes(0);
                let listHtml =
                    `<li data-id="-1" class=""><span class="group-name">Select Group</span></li>`;
                    //<li data-id="0" class="${nogr ? 'selected' : ''}"><span class="group-name">NO_GROUP</span></li>-->
                
                mockData.forEach(group => {
                    let isSelected = selectedGroups.includes(group.id);
                    listHtml += `
                    <li data-id="${group.id}" class="${isSelected ? 'selected' : ''}">
                        <span class="group-name">${group.name}</span>
                        <span class="font-12 m-r-5">${group.username}</span>
                        <span class="font-12 m-r-5 font-italic w-80px text-center">${group.channels} channels</span>
                        <div class="action-icons">
                            <i class="fa fa-solid fa-arrow-up go-top-btn" title="Move to top"></i>
                            <i class="fa fa-solid fa-pencil-square-o edit-btn"></i>
                            <i class="fa fa-solid fa-trash delete-btn"></i>
                            ${isSelected ? '<i class="fa fa-solid fa-check"></i>' : ''}
                        </div>
                    </li>`;
                });
                
                $list.html(listHtml);
                updateDropdownText();
            }
            
            // Update dropdown button text
            function updateDropdownText() {
                let selectedNames = mockData
                    .filter(group => selectedGroups.includes(group.id))
                    .map(group => group.name)
                    .join(", ");
                $dropdown.text(selectedNames || "Select Group");
            }
            
            // Store public methods for external access
            $container.data('groupDropdown', {
                fetchGroups: fetchGroups,
                getSelectedGroups: function() {
                    return [...selectedGroups];
                },
                setSelected: function(ids) {
                    if (Array.isArray(ids)) {
                        selectedGroups = ids.map(id => Number(id));
                    } else {
                        selectedGroups = [Number(ids)];
                    }
                    $input.val(settings.multipleSelect ? selectedGroups.join(',') : selectedGroups[0]);
                    loadGroups();
                    updateDropdownText();
                    return selectedGroups;
                }
            });            
            
            // Initialize the dropdown
            fetchGroups();
            
            // Event Handlers
            
            // Toggle dropdown on button click
            $dropdown.on("click", function(event) {
                event.stopPropagation();
                $menu.toggle();
                
                // Close other menus
                $(`.select-dropdown-menu:not(#${menuId})`).hide();
            });
            
            // Close dropdown when clicking outside
            $(document).on("click", function(event) {
                if (!$(event.target).closest(`#${containerId}, .jconfirm-box`).length) {
                    if (!settings.multipleSelect) {
                        $menu.hide();
                    }
                }
            });
            
            // Select group
            $list.on("click", "li", function() {
                let id = $(this).data("id");
                
                if (settings.multipleSelect) {
                    if (selectedGroups.includes(id)) {
                        selectedGroups = selectedGroups.filter(gid => gid !== id);
                    } else {
                        selectedGroups.push(id);
                    }
                } else {
                    selectedGroups = [id];
                    $input.val(id);
                    $menu.hide();
                }
                
                loadGroups();
                
                // Execute callback
                if (typeof settings.onSelect === 'function') {
                    settings.onSelect(selectedGroups);
                }
            });
            
            // Edit group
            $list.on("click", ".edit-btn", function(event) {
                event.stopPropagation();
                let li = $(this).closest("li");
                let id = li.data("id");
                let name = li.find(".group-name").text();
                
                $.confirm({
                    title: "Update Group",
                    content: '<input type="text" id="editGroupName" value="' + name + '" class="form-control">',
                    buttons: {
                        "Cancel": function() {},
                        "Update": {
                            btnClass: "btn-blue",
                            action: function() {
                                let newName = this.$content.find("#editGroupName").val().trim();
                                if (newName && newName !== name) {
                                    mockData = mockData.map(group => group.id === id ? {
                                        ...group,
                                        name: newName
                                    } : group);
                                    loadGroups();
                                }
                                $.ajax({
                                    url: settings.updateUrl,
                                    method: "GET",
                                    data: {
                                        id: id,
                                        group_name: newName
                                    },
                                    success: function(response) {
                                        console.log("Update response:", response);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error updating:", error);
                                    }
                                });
                            }
                        }
                    }
                });
            });
            
            // Delete group
            $list.on("click", ".delete-btn", function(event) {
                event.stopPropagation();
                let groupId = $(this).closest("li").data("id");
                
                $.confirm({
                    animation: 'rotateXR',
                    title: "Confirm",
                    content: "Are you sure you want to delete this group?",
                    buttons: {
                        confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-red',
                            action: function() {
                                $.ajax({
                                    url: settings.deleteUrl,
                                    method: "GET",
                                    data: {
                                        id: groupId
                                    },
                                    success: function(response) {
                                        console.log("Delete response:", response);
                                        mockData = mockData.filter(group => group.id !== groupId);
                                        loadGroups();
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error deleting:", error);
                                    }
                                });
                            }
                        },
                        cancel: function() {}
                    }
                });
            });
            
            // Search functionality
            $search.on("keyup", function() {
                let value = $(this).val().toLowerCase();
                $list.find("li").each(function() {
                    let text = $(this).find(".group-name").text().toLowerCase();
                    $(this).toggle(text.includes(value));
                });
            });
            
            // Make list sortable
            $list.sortable({
                update: function(event, ui) {
                    // Get the new order of IDs
                    let order = [];
                    $list.find("li").each(function() {
                        order.push($(this).data("id"));
                    });
                    
                    // Send the new order to the server
                    $.ajax({
                        url: settings.updateUrl,
                        method: "GET",
                        data: {
                            order: order
                        },
                        success: function(response) {
                            console.log("Order update successful:", response);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error updating order:", error);
                        }
                    });
                }
            }).disableSelection();
            
            //go to top
            $list.on("click", ".go-top-btn", function(event) {
                event.stopPropagation();
                const li = $(this).closest("li");
                const groupId = Number(li.data("id"));

                // Ignore clicks on default items
                if (groupId <= 0) return;

                // Get the current order of groups
                const currentOrder = [];
                $list.find("li").each(function() {
                    currentOrder.push(Number($(this).data("id")));
                });

                // Remove the clicked group ID from the current order
                const newOrder = currentOrder.filter(id => id !== groupId);

                // Add the clicked group ID at the beginning (after default items)
                const defaultItems = newOrder.filter(id => id <= 0);
                const otherItems = newOrder.filter(id => id > 0);

                // Create the final order
                const finalOrder = [...defaultItems, groupId, ...otherItems];

                // Reorder the mockData array
                mockData = finalOrder
                    .filter(id => id > 0)
                    .map(id => mockData.find(item => Number(item.id) === id))
                    .filter(Boolean);

                // Reload the groups with new order
                loadGroups();
                updateDropdownText();

                // Send the new order to the server
                $.ajax({
                    url: settings.updateUrl,
                    method: "GET",
                    data: {
                        order: finalOrder
                    },
                    success: function(response) {
                        console.log("Order update successful:", response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error updating order:", error);
                    }
                });
            });
        });
    };
})(jQuery);