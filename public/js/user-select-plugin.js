(function($) {
    // Add CSS to the document
    var css = `
    .user-select-container {
      position: relative;
      width:100%;
    }
    .user-select-dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      z-index: 1000;
      display: none;
      background-color: #fff;
      border: 1px solid #ddd;
      max-height: 400px;
      overflow-y: auto;
    }
    .user-select-dropdown ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .user-select-dropdown li {
      padding: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
    }
    .user-select-dropdown li:hover {
      background-color: #f1f1f1;
    }
    .user-select-dropdown li img {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      margin-right: 10px;
    }
    .selected-users {
      display: flex;
      flex-wrap: wrap;
    }
    .selected-user {
      position: relative;
      margin-right: -10px;
      z-index: 1;
    }
    .selected-user img {
      width: 30px;
      height: 30px;
      border-radius: 50%;
    }
    .selected-user .remove-user {
      position: absolute;
      top: 0;
      left: 0;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      display: none;
      justify-content: center;
      align-items: center;
      cursor: pointer;
    }
    .selected-user:hover .remove-user {
      display: flex;
    }
    .selected-user:hover {
      z-index: 10;
    }
    .user-select-search {
      padding: 8px;
      border-bottom: 1px solid #ddd;
    }
    .user-select-search input {
      width: 100%;
      padding: 5px;
      box-sizing: border-box;
    }
    `;

    var style = document.createElement('style');
    style.type = 'text/css';
    if (style.styleSheet) {
      style.styleSheet.cssText = css;
    } else {
      style.appendChild(document.createTextNode(css));
    }
    document.head.appendChild(style);

    $.fn.userSelect = function(options) {
      var settings = $.extend({
        users: [],
        selectedUserIdsInput: '', // Default input selector for storing selected user IDs
        onSelect: function() {},
        onRemove: function() {}
      }, options);

      return this.each(function() {
        var $button = $(this);
        var $selectedUserIds = $(settings.selectedUserIdsInput);

        // Create the necessary HTML elements dynamically
        var $container = $('<div class="user-select-container"></div>');
        var $dropdown = $(`
          <div class="user-select-dropdown">
            <div class="user-select-search">
              <input type="text" class="form-control" placeholder="Search...">
            </div>
            <ul class="user-list"></ul>
          </div>`);
        var $selectedUsers = $('<div class="selected-users"></div>');

        $container.append($dropdown);
        $container.append($selectedUsers);
        $button.before($container);

        var $userList = $dropdown.find('.user-list');

        // Populate the user dropdown
        settings.users.forEach(function(user) {
          $userList.append('<li class="li-user" data-username="' + user.username + '"><img class="img-cover" src="' + user.avatar + '" alt="">' + user.username + '</li>');
        });

        // Toggle dropdown on button click
        $button.on('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          $dropdown.toggle();
          $("#modal-filter").toggleClass("overflow-visible");
        });

        // Search functionality
        $dropdown.find('.user-select-search input').on('keyup', function() {
          var searchTerm = $(this).val().toLowerCase();
          $userList.find('li').each(function() {
            var username = $(this).data('username').toLowerCase();
            if (username.includes(searchTerm)) {
              $(this).show();
            } else {
              $(this).hide();
            }
          });
        });

        // Select user from dropdown
        $userList.on('click', 'li', function() {
          var $this = $(this);
          var username = $this.data('username');
          var userAvatar = $this.find('img').attr('src');

          if ($selectedUsers.find('div[data-username="' + username + '"]').length === 0) {
            $selectedUsers.append(
              '<div class="selected-user" data-username="' + username + '" title="' + username + '">' +
              '<img class="img-cover" src="' + userAvatar + '" alt="">' +
              '<span class="remove-user">&times;</span>' +
              '</div>'
            );

//            $('[data-toggle="tooltip"]').tooltip(); // Initialize tooltips

            $this.remove(); // Remove user from dropdown list

            var selectedIds = $selectedUserIds.val() ? $selectedUserIds.val().split(',') : [];
            selectedIds.push(username);
            $selectedUserIds.val(selectedIds.join(','));

            settings.onSelect(username);
          }

//          $dropdown.hide();
        });

        // Remove user from selected list
        $selectedUsers.on('click', '.remove-user', function() {
          var $parent = $(this).parent();
          var username = $parent.data('username');
          var userAvatar = $parent.find('img').attr('src');

          // Add user back to dropdown list
          $userList.append('<li data-username="' + username + '"><img class="img-cover" src="' + userAvatar + '" alt="">' + username + '</li>');

          $parent.remove();
          $('[data-toggle="tooltip"]').tooltip('dispose'); // Dispose tooltips
          $('[data-toggle="tooltip"]').tooltip(); // Reinitialize tooltips

          var selectedIds = $selectedUserIds.val().split(',');
          selectedIds = selectedIds.filter(function(id) {
            return id != username;
          });
          $selectedUserIds.val(selectedIds.join(','));

          settings.onRemove(username);
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
          if (!$(e.target).closest('.user-select-container').length && !$(e.target).hasClass("li-user")) {
            $dropdown.hide();
            $("#modal-filter").removeClass("overflow-visible");
          }else{
            $("#modal-filter").addClass("overflow-visible");
          }
        });
      });
    };
  }(jQuery));