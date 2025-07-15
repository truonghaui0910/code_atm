@extends('layouts.master')

@section('content')
    <link href="css/album.css" rel="stylesheet" type="text/css" />
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm mb-4">
            <div class="search-container" id="album-search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="form-control" placeholder="Search albums..." id="album-search">

            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <!--<a class="nav-link" href="#" id="show-all-albums">All Albums</a>-->
                        @if ($is_admin_music)
                            <button class="btn btn-create-album-style" id="artist-management-btn"
                                style="margin-right: 15px;">
                                <i class="fas fa-users"></i> Artist
                            </button>
                        @endif
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-create-album btn-create-album-style ml-3">
                            <i class="fas fa-plus mr-1"></i> Create Album
                        </button>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="row">
            <div class="col-lg-12 mb-4" id="albums-container">
                <div id="album-controls-header" class="d-flex justify-content-between align-items-center mb-4">
                    <div class="filter-section">
                        <div class="filter-buttons">
                            <button type="button" class="filter-btn active" data-status="all">All <span
                                    class="filter-count">(0)</button>
                            <button type="button" class="filter-btn filter-btn-not-distributed"
                                data-status="not-distributed">Not Distributed <span class="filter-count">(0)</button>
                            <button type="button" class="filter-btn filter-btn-pending" data-status="pending">Pending <span
                                    class="filter-count">(0)</button>
                            <button type="button" class="filter-btn filter-btn-distributing"
                                data-status="distributing">Distributing <span class="filter-count">(0)</button>
                            <button type="button" class="filter-btn filter-btn-distributed"
                                data-status="distributed">Distributed <span class="filter-count">(0)</button>
                            <button type="button" class="filter-btn filter-btn-error" data-status="error">Error <span
                                    class="filter-count">(0)</button>
                            <button type="button" class="filter-btn filter-btn-online" data-status="online">Online <span
                                    class="filter-count">(0)</button>
                        </div>
                    </div>
                    <div class="view-switcher">
                        <button class="view-btn active" data-view="grid" title="Grid view">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" data-view="table" title="Table view">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <div class="row" id="albums-list">

                </div>
            </div>


            <div class="col-lg-12 album-details-container" id="album-details">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="album-detail-title">
                                <button class="btn btn-sm btn-light mr-2" id="back-to-albums">
                                    <i class="fas fa-arrow-left"></i> Back
                                </button>
                                <span class="h5 mb-0" id="current-album-name">Album Name</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <img alt="Album cover" class="img-fluid rounded" id="current-album-cover">
                                <div class="mt-3">
                                    <p class="text-muted" id="current-album-desc">Album description</p>
                                    <p class="mb-1"><small class="text-muted">Songs: <span
                                                id="song-count">0</span></small></p>
                                    <p><small class="text-muted">Status: <span id="current-album-status"
                                                class="badge badge-secondary">Not Distributed</span></small></p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Songs List</h5>
                                    <div class="search-container">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="form-control form-control-sm"
                                            placeholder="Search songs..." id="album-songs-search" autocomplete="off">
                                    </div>
                                </div>
                                <div class="songs-list" id="album-songs-list">
                                    <!-- Album songs will be listed here -->
                                    <div class="text-center py-5 text-muted" id="empty-album-message">
                                        <i class="fas fa-music fa-3x mb-3"></i>
                                        <p class="w-90">This album has no songs yet</p>
                                        <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                            data-target="#addSongsModal">
                                            Add Songs Now
                                        </button>
                                    </div>
                                </div>

                                <!-- Audio Player -->
                                <div class="audio-player mt-4 d-none" id="audio-player">
                                    <div class="player-controls mb-2">
                                        <button class="btn btn-play" id="player-play-btn">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <div class="player-song-info">
                                            <div class="font-weight-bold" id="player-song-title">Song Title</div>
                                            <small class="text-muted" id="player-song-artist">Artist Name</small>
                                        </div>
                                        <span class="text-muted" id="player-time">0:00 / 0:00</span>
                                    </div>
                                    <div class="progress">
                                        <div id="player-progress" class="progress-bar bg-primary" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                    <audio id="audio-element" src=""></audio>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding songs to album -->
    <div class="modal fade" id="addSongsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex justify-content-sm-between align-items-center w-100">
                        <div>
                            <h5 class="modal-title mt-0 d-flex align-items-center">
                                <span id="created_by_avatar"></span>
                                <strong id="dialog-task-title">Add songs to album</strong>
                            </h5>
                        </div>
                        <div class="">

                            <button class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light "
                                data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
                                style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                    <path
                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group search-container mb-4">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Search songs..." id="song-search">
                    </div>
                    <div class="songs-list" id="available-songs-list">

                    </div>
                </div>
                <div class="modal-footer">
                    <span class="mr-auto"><span id="selected-count">0</span> songs selected</span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="add-songs-btn">Add Songs</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="artistStatsModal" tabindex="-1" role="dialog" aria-labelledby="artistStatsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-80" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="artistStatsModalLabel">Artist Stats</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="min-height:800px;">
                    <iframe id="artist-stats-iframe" src="about:blank" width="100%" height="700"
                        frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="artistManagementModal" tabindex="-1" role="dialog"
        aria-labelledby="artistManagementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex justify-content-sm-between align-items-center w-100">
                        <div>
                            <h5 class="modal-title mt-0 d-flex align-items-center">
                                <span id="created_by_avatar"></span>
                                <strong id="dialog-task-title">Artist Management</strong>
                            </h5>
                        </div>
                        <div class="">

                            <button class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light "
                                data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
                                style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                    <path
                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <!-- Filter Controls -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="artist-name-filter">Filter by Artist Name:</label>
                                <input type="text" class="form-control" id="artist-name-filter"
                                    placeholder="Enter artist name...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="youtube-claim-filter">YouTube Claim Status:</label>
                                <select class="form-control" id="youtube-claim-filter">
                                    <option value="">All</option>
                                    <option value="1">Enabled</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="artist-sort">Sort by:</label>
                                <select class="form-control" id="artist-sort">
                                    <option value="artist_name|asc">Artist Name (A-Z)</option>
                                    <option value="artist_name|desc">Artist Name (Z-A)</option>
                                    <option value="artist_total_streams|desc">Total Streams (High to Low)</option>
                                    <option value="artist_total_streams|asc">Total Streams (Low to High)</option>
                                    <option value="id|desc">Newest First</option>
                                    <option value="id|asc">Oldest First</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="artist-filter-btn">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Artist Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="artist-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Artist Name</th>
                                    <th>Total Streams</th>
                                    <th>YouTube Claim</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody id="artist-table-body">
                                <!-- Artist data will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span id="artist-pagination-info">Showing 0 to 0 of 0 results</span>
                        </div>
                        <nav aria-label="Artist pagination">
                            <ul class="pagination mb-0" id="artist-pagination">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>

                    <!-- Loading indicator -->
                    <div class="text-center" id="artist-loading" style="display: none;">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('dialog.bom.add_album')
    @include('dialog.bom.add_artist')
@endsection

@section('script')
    <script type="text/javascript">
        artistList();
        $(document).on('click', '.btn-edit-album', function(e) {
            e.preventDefault();
            // Lấy thông tin album hiện tại từ biến album (hoặc từ DOM nếu cần)
            const album = window.currentAlbumData; // hoặc lấy từ albums array
            // Đổ dữ liệu vào form modal

            $('#addAlbumModal input[name="album_id"]').val(album.id);
            $('#addAlbumModal input[name="edit_mode"]').val('1');
            $('#albumTitle').val(album.name)
            $('#albumArtist').val(album.artist_id).selectpicker('refresh');
            $('#instruments').val(album.instruments).selectpicker('refresh');
            $('#addAlbumModal input[name="releaseDate"]').val(album.releaseDate);
            let genreValue = null;
            $('#albumGenre option').each(function() {
                // Match by label or value
                if ($(this).text().trim() === album.genre.trim() || $(this).val() == album.genre || $(this)
                    .val() == album.genre_id) {
                    genreValue = $(this).val();
                    return false; // break
                }
            });
            if (genreValue) {
                $('#albumGenre').val(genreValue).trigger('change');
            }

            // Set album cover preview
            if (album.coverImg) {
                $('#imagePreview').html(
                    `<img src="${album.coverImg}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">`
                    );
            } else {
                $('#imagePreview').html(`<i class="fas fa-image fa-3x music-icon"></i>`);
            }

            $('#addAlbumModal').modal('show');
        });

        $(document).on('click', '.btn-artist-stats', function(e) {
            e.stopPropagation();
            const artistName = $(this).data('artist');
            const chartUrl = `https://distro.360promo.fm/iframe/charts/${artistName}`;
            $('#artist-stats-iframe').attr('src', chartUrl);
            $('#artistStatsModal').modal('show');
        });

        $('#artistStatsModal').on('hidden.bs.modal', function() {
            $('#artist-stats-iframe').attr('src', 'about:blank');
        });

        function setupLazyLoading() {
            // Intersection Observer cho lazy loading ảnh
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src;

                        if (src) {
                            // Kiểm tra xem đây là grid hay table view
                            const isTableView = img.closest('.table-album-cover-wrapper') !== null;
                            const wrapper = isTableView ?
                                img.closest('.table-album-cover-wrapper') :
                                img.closest('.album-cover-wrapper');

                            // Tìm loading spinner
                            const spinner = isTableView ?
                                wrapper.querySelector('.table-image-loading-spinner') :
                                wrapper.querySelector('.image-loading-spinner');

                            // Hiển thị loading spinner
                            if (spinner) {
                                spinner.style.display = 'block';
                            }

                            // Tạo temporary image để load
                            const tempImg = new Image();

                            tempImg.onload = function() {
                                // Ảnh load thành công
                                img.src = src;
                                img.classList.remove('lazy-loading');
                                img.classList.add('loaded');

                                // Ẩn loading spinner
                                if (spinner) {
                                    spinner.style.display = 'none';
                                }

                                // Ẩn placeholder sau một chút để có effect mượt
                                setTimeout(() => {
                                    const placeholder = isTableView ?
                                        wrapper.querySelector('.table-cover-placeholder') :
                                        wrapper.querySelector('.album-cover-placeholder');

                                    if (placeholder) {
                                        placeholder.style.opacity = '0';
                                    }
                                }, 100);
                            };

                            tempImg.onerror = function() {
                                // Ảnh load lỗi - hiển thị placeholder lỗi
                                img.src = '/default-album-cover.png'; // Hoặc ảnh mặc định khác
                                img.classList.remove('lazy-loading');
                                img.classList.add('error');

                                // Ẩn loading spinner
                                if (spinner) {
                                    spinner.style.display = 'none';
                                }

                                // Thay đổi placeholder thành error state
                                const placeholder = isTableView ?
                                    wrapper.querySelector('.table-cover-placeholder') :
                                    wrapper.querySelector('.album-cover-placeholder');

                                if (placeholder) {
                                    placeholder.classList.add('image-error-placeholder');
                                    placeholder.innerHTML =
                                        '<i class="fas fa-exclamation-triangle"></i>';
                                }
                            };

                            // Bắt đầu load ảnh
                            tempImg.src = src;
                        }

                        // Ngừng observe image này
                        observer.unobserve(img);
                    }
                });
            }, {
                // Cấu hình Observer
                rootMargin: '50px 0px', // Load ảnh khi cách viewport 50px
                threshold: 0.1 // Trigger khi 10% ảnh visible
            });

            // Observe tất cả ảnh có data-src (cả grid và table)
            const lazyImages = document.querySelectorAll('img[data-src]');
            lazyImages.forEach(img => {
                imageObserver.observe(img);
            });

            const currentView = getViewPreference();
            console.log(`Lazy loading initialized for ${lazyImages.length} images in ${currentView} view`);
        }

        function loadGroups() {
            return $.ajax({
                    url: '/groups/list',
                    type: 'GET',
                    dataType: 'json'
                })
                .then(function(response) {
                    if (Array.isArray(response)) {
                        const groupSelect = $('#group-filter');
                        groupSelect.find('option:not(:first)').remove(); // Keep the "All Groups" option

                        //                    response.forEach(group => {
                        //                        groupSelect.append(`<option value="${group.id}" data-content='${group.name}'>${group.name}</option>`);
                        //                    });

                        var option = `<option value="-1">All Groups</option>`;
                        $.each(response, function(k, v) {
                            option +=
                                `<option value='${v.id}' data-content='${v.name}'></option>`;
                        });
                        $("select#group-filter").empty();
                        $("select#group-filter").html(option);
                        $('select#group-filter').selectpicker('refresh');

                        return response;
                    } else {
                        console.error('Invalid groups data format:', response);
                        return [];
                    }
                })
                .catch(function(error) {
                    console.error('Error loading groups:', error);
                    return [];
                });
        }

        function setupGroupFilter() {
            // Load groups when the modal is opened
            $('#addSongsModal').on('show.bs.modal', function() {
                // Load groups from API
                loadGroups();

                // Initialize with no group filter
                renderAvailableSongs();
            });

            // Handle group filter changes
            $(document).on('change', '#group-filter', function() {
                const groupId = $(this).val();
                renderAvailableSongs(groupId);
            });
        }

        function modalAddArtist() {
            $("#form-add-artist")[0].reset();
            $("#artist-validation-feedback").html("");
            $("#edit_album_id").val("");
            $("#artist_id").val("");
            $("#dialog_add_artist").modal("show");

        }

        function addArtist() {
            var form = $("#form-add-artist").serialize();
            const saveBtn = $(".btn-save-artist");
            saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "/albumAddArtist",
                data: form,
                dataType: 'json',
                success: function(data) {
                    logger('addArtist', data);
                    showNotification(data.message, data.status);
                    saveBtn.html('<i class="fa fa-save"></i> Save').prop('disabled', false);

                    if (data.status == "success") {
                        // If this was an edit operation (album_id was provided)
                        const albumId = $('#edit_album_id').val();
                        if (albumId) {
                            // Update the album artist UI
                            const newArtistName = $('#artist_name').val();
                            updateArtistNameUI(albumId, newArtistName);
                        } else {
                            // Original functionality - refresh artist list
                            artistList();
                        }

                        // Reset form and close modal
                        $("#form-add-artist")[0].reset();
                        $("#dialog_add_artist").modal("hide");
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    saveBtn.html('<i class="fa fa-save"></i> Save').prop('disabled', false);
                }
            });
        }

        function artistList() {
            $.ajax({
                type: "GET",
                url: "/albumListArtist",
                data: {

                },
                dataType: 'json',
                success: function(data) {
                    logger('artistList', data);
                    //gen html cho selectbox
                    var option = "";
                    $.each(data, function(k, v) {
                        option +=
                            `<option value='${v.id}'  data-content='${v.artist_name}'></option>`;
                    });
                    $("select.albumArtist").empty();
                    $("select.albumArtist").html(option);
                    $('select.albumArtist').selectpicker('refresh');


                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }

        function updateFilterCounts() {
            const statusCounts = {
                'all': albums.length,
                'not-distributed': 0,
                'pending': 0,
                'distributing': 0,
                'distributed': 0,
                'error': 0,
                'online': 0
            };

            // Đếm số lượng cho mỗi status
            albums.forEach(album => {
                switch (album.distributed) {
                    case 0:
                        statusCounts['not-distributed']++;
                        break;
                    case 1:
                        statusCounts['pending']++;
                        break;
                    case 2:
                        statusCounts['distributing']++;
                        break;
                    case 3:
                        statusCounts['distributed']++;
                        break;
                    case 4:
                        statusCounts['error']++;
                        break;
                    case 5:
                        statusCounts['online']++;
                        break;
                }
            });

            // Cập nhật hiển thị trên buttons
            $('.filter-btn').each(function() {
                const status = $(this).data('status');
                const count = statusCounts[status] || 0;
                $(this).find('.filter-count').text(`(${count})`);
            });
        }

        function fetchAlbums() {
            // Hiển thị loading
            $('#albums-list').html(
                '<div class="col-12 text-center my-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 col-md-12">Loading albums...</p></div>'
            );

            // Gọi API lấy danh sách album
            $.ajax({
                url: '/getListAlbum',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response && Array.isArray(response)) {
                        // Lưu trữ danh sách album vào biến toàn cục
                        albums = response;
                        // Render danh sách album
                        renderAlbumsWithView();
                    } else {
                        // Hiển thị thông báo lỗi
                        $('#albums-list').html(
                            '<div class="col-12 text-center my-5"><i class="fas fa-exclamation-triangle text-warning fa-2x"></i><p class="mt-2 col-md-12">Failed to load albums. Invalid data format.</p></div>'
                        );
                        console.error('Invalid album data format:', response);
                    }
                },
                error: function(xhr, status, error) {
                    // Hiển thị thông báo lỗi
                    $('#albums-list').html(
                        '<div class="col-12 text-center my-5"><i class="fas fa-exclamation-triangle text-danger fa-2x"></i><p class="mt-2 col-md-12">Failed to load albums. Please try again later.</p></div>'
                    );
                    console.error('Error fetching albums:', error);
                }
            });
        }

        function fetchAlbumDetails(albumId, callback) {
            // Tạo hai promises để gọi song song hai API
            const albumPromise = $.ajax({
                url: `/getAlbum?id=${albumId}`,
                type: 'GET',
                dataType: 'json'
            });

            const songsPromise = $.ajax({
                url: `/getSongsByAlbum?id=${albumId}`,
                type: 'GET',
                dataType: 'json'
            });

            // Sử dụng Promise.all để đợi cả hai requests hoàn thành
            Promise.all([albumPromise, songsPromise])
                .then(([albumData, songsResponse]) => {
                    if (albumData && albumData.id) {
                        // Chuyển đổi dữ liệu từ API getAlbum sang định dạng cần thiết

                        let instruments = [];
                        if (albumData.instruments) {
                            try {
                                // If instruments are stored as a JSON string, parse them
                                instruments = typeof albumData.instruments === 'string' ?
                                    JSON.parse(albumData.instruments) :
                                    albumData.instruments;
                            } catch (e) {
                                console.error('Error parsing instruments:', e);
                                instruments = [];
                            }
                        }
                        const processedAlbum = {
                            id: albumData.id,
                            name: albumData.album_name,
                            description: albumData.desc,
                            artist_id: albumData.artist_id,
                            artist: albumData.artist,
                            genre: albumData.genre_name,
                            releaseDate: albumData.release_date,
                            coverImg: albumData.album_cover,
                            distributed: albumData.is_released,
                            status: albumData.status,
                            distroReleaseDate: albumData.distro_release_date,
                            username: albumData.username,
                            instruments: instruments,
                            spotify_info: JSON.parse(albumData.spotify_info)
                        };

                        // Gán danh sách bài hát vào thông tin album
                        processedAlbum.songs = Array.isArray(songsResponse) ? songsResponse : [];

                        // Trả về kết quả
                        callback(null, processedAlbum);
                    } else {
                        callback('Invalid album data or album not found', null);
                    }
                })
                .catch(error => {
                    console.error('Error fetching album details:', error);
                    callback(error, null);
                });
        }

        function fetchAvailableSongs(groupId = null) {
            const url = '/getSongsForRelease';
            const data = {};

            // Add group_id to the request if provided
            if (groupId) {
                data.group_id = groupId;
            }

            return $.ajax({
                url: url,
                type: 'GET',
                data: data,
                dataType: 'json'
            });
        }

        function removeSongFromAlbum(songId, albumId) {
            return $.ajax({
                url: '/deleteSongFromAlbum',
                type: 'GET',
                data: {
                    song_id: songId,
                    album_id: albumId
                },
                dataType: 'json'
            });
        }

        function handleAddMultipleSongsToAlbum() {
            console.log('handleAddMultipleSongsToAlbum called');
            console.log('selectedSongs:', selectedSongs);
            console.log('currentAlbumId:', currentAlbumId);

            if (selectedSongs.length === 0 || !currentAlbumId) {
                console.log('Validation failed');
                showNotification('Please select at least one song', 'warning');
                return;
            }

            // Hiển thị loading
            $('#add-songs-btn').html('<i class="fas fa-spinner fa-spin"></i> Adding...').prop('disabled', true);

            const requestData = {
                song_ids: selectedSongs,
                album_id: currentAlbumId,
                _token: $('input[name="_token"]').attr('value')
            };

            console.log('About to send request with data:', requestData);

            // Gọi AJAX trực tiếp
            $.ajax({
                url: '/addSongsToAlbum',
                type: 'POST',
                data: requestData,
                dataType: 'json',
                beforeSend: function(xhr) {
                    console.log('Request about to be sent');
                },
                success: function(response) {
                    console.log('Response received:', response);
                    if (response.status === 'success') {
                        // Lưu lại filters hiện tại
                        const currentGroupId = $('#group-filter').val();

                        // Cập nhật giao diện
                        fetchAlbumDetails(currentAlbumId, function(error, album) {
                            if (!error && album) {
                                // Cập nhật album hiện tại
                                const albumIndex = albums.findIndex(a => a.id === currentAlbumId);
                                if (albumIndex !== -1) {
                                    albums[albumIndex] = album;
                                }

                                // Render lại album details
                                showAlbumDetails(currentAlbumId);

                                // Render lại danh sách album
                                renderAlbums();

                                // Clear selected songs but keep filters
                                selectedSongs = [];
                                updateSelectedCount();

                                // Re-render available songs with current filters
                                renderAvailableSongs(currentGroupId);

                                // Hiển thị thông báo thành công
                                showNotification(response.message, 'success');
                            }
                        });
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        xhr,
                        status,
                        error
                    });

                    // Xử lý lỗi validation cụ thể
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showNotification(xhr.responseJSON.message, 'error');
                    } else {
                        showNotification('Failed to add songs to album. Please try again.', 'error');
                    }
                },
                complete: function() {
                    // Reset button state
                    $('#add-songs-btn').html('Add Songs').prop('disabled', false);
                }
            });
        }

        function validateSelectedSongs() {
            if (selectedSongs.length === 0) {
                showNotification('Please select at least one song', 'warning');
                return false;
            }

            // Kiểm tra trùng tên trong danh sách đã chọn
            const selectedSongElements = $('.modal-song-item.selected');
            const songNames = [];
            const duplicates = [];

            selectedSongElements.each(function() {
                const songName = $(this).find('.modal-song-title').text().trim().toLowerCase();
                if (songNames.includes(songName)) {
                    duplicates.push($(this).find('.modal-song-title').text().trim());
                } else {
                    songNames.push(songName);
                }
            });

            if (duplicates.length > 0) {
                showNotification('You have selected songs with duplicate names: ' + duplicates.join(', '), 'warning');
                return false;
            }

            return true;
        }

        function handleRemoveSongFromAlbum(songId, albumId) {

            $.confirm({
                title: 'Remove Song',
                content: `Are you sure you want to remove this song from the album?`,
                zIndex: 1,
                buttons: {
                    "Cancel": function() {},
                    "Remove": {
                        btnClass: "btn-danger",
                        action: function() {
                            // Hiển thị loading trên nút xóa
                            $(`.remove-song-btn[data-song-id="${songId}"]`).html(
                                '<i class="fas fa-spinner fa-spin"></i>');

                            // Gọi API xóa bài hát
                            removeSongFromAlbum(songId, albumId)
                                .then(response => {
                                    // Cập nhật giao diện
                                    fetchAlbumDetails(albumId, function(error, album) {
                                        if (!error && album) {
                                            // Cập nhật album hiện tại
                                            const albumIndex = albums.findIndex(a => a.id ===
                                                albumId);
                                            if (albumIndex !== -1) {
                                                albums[albumIndex] = album;
                                            }

                                            // Render lại album details
                                            showAlbumDetails(albumId);

                                            // Render lại danh sách album
                                            renderAlbums();

                                            // Hiển thị thông báo thành công
                                            showNotification('Song removed from album successfully',
                                                'success');
                                        }
                                    });
                                })
                                .catch(error => {
                                    console.error('Error removing song from album:', error);
                                    showNotification('Failed to remove song from album. Please try again.',
                                        'error');

                                    // Reset nút xóa
                                    $(`.remove-song-btn[data-song-id="${songId}"]`).html(
                                        '<i class="fas fa-times"></i>');
                                });
                        }
                    }
                }
            });
        }

        function setupAlbumSearch() {
            //            console.log('Setting up album search'); // Log để debug

            // Xóa sự kiện cũ (nếu có) để tránh trùng lặp
            $('#album-search').off('input');

            // Thêm sự kiện mới
            $('#album-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase().trim();
                //                console.log('Search term:', searchTerm); // Log term tìm kiếm

                // Tìm kiếm qua tất cả card album
                $('.card.album-card').each(function() {
                    const cardElement = $(this);
                    const parentElement = cardElement.closest('.col-md-3');

                    const albumTitle = cardElement.find('.album-title').text().toLowerCase();
                    const albumArtist = cardElement.find('.album-meta-item').eq(0).text().toLowerCase();
                    const albumDate = cardElement.find('.album-meta-item').eq(1).text().toLowerCase();
                    const albumSongs = cardElement.find('.album-meta-item').eq(2).text().toLowerCase();
                    const albumGenre = cardElement.find('.album-genre').text().toLowerCase();
                    const albumStatus = cardElement.find('.album-status-badge').text().toLowerCase();
                    //                const albumUsername = cardElement.find('.album-username').text().toLowerCase();
                    const albumUsername = cardElement.find('.user-avatar').attr('title').toLowerCase();

                    // Hiển thị/ẩn dựa trên kết quả tìm kiếm
                    if (albumTitle.includes(searchTerm) ||
                        albumArtist.includes(searchTerm) ||
                        albumDate.includes(searchTerm) ||
                        albumSongs.includes(searchTerm) ||
                        albumStatus.includes(searchTerm) ||
                        albumUsername.includes(searchTerm) ||
                        albumGenre.includes(searchTerm)) {
                        parentElement.show();
                    } else {
                        parentElement.hide();
                    }
                });
            });

        }

        function renderAlbums() {
            const albumsContainer = $('#albums-list');
            albumsContainer.empty();

            // Check if there are no albums
            if (!albums.length) {
                const emptyStateHTML = `
                <div class="col-12">
                    <div class="empty-state-container">
                        <div class="empty-state-icon">
                            <i class="fas fa-compact-disc"></i>
                        </div>
                        <h3 class="empty-state-title">Your album collection is empty</h3>
                        <p class="empty-state-description">
                            Start building your music collection by creating your first album. 
                            You can add songs, set details, and distribute your albums to share your music with the world.
                        </p>
                        <button class="btn btn-create-first-album" data-toggle="modal" data-target="#addAlbumModal">
                            <i class="fas fa-plus-circle"></i> Create Your First Album
                        </button>
                    </div>
                </div>
            `;
                albumsContainer.html(emptyStateHTML);

                // Assign event for the create album button
                $('.btn-create-first-album').click(function() {
                    $('#addAlbumModal').modal('show');
                });

                return; // No need to process the rest of the function
            }
            console.log(albums);

            // Display albums list (only runs when there's at least 1 album)
            albums.forEach(album => {
                const songCount = album.songs.length;

                // Updated distributeStatus logic to reflect new is_released values
                let distributeStatus = '';
                let statusClass = '';

                switch (album.distributed) {
                    case 0: // not distribute
                        distributeStatus =
                            `<span class="badge badge-secondary album-status-badge">Not Distributed</span>`;
                        statusClass = 'badge-secondary';
                        break;
                    case 1: // pending distribute
                        distributeStatus =
                            `<span class="badge badge-warning album-status-badge">Pending Distribution</span>`;
                        statusClass = 'badge-warning';
                        break;
                    case 2: // distributing
                        distributeStatus = `<span class="badge badge-info album-status-badge">Distributing</span>`;
                        statusClass = 'badge-info';
                        break;
                    case 3: // distributed
                        distributeStatus =
                        `<span class="badge badge-success album-status-badge">Distributed</span>`;
                        statusClass = 'badge-success';
                        break;
                    case 4: // error distribute
                        distributeStatus = `<span class="badge badge-danger album-status-badge">Error</span>`;
                        statusClass = 'badge-danger';
                        break;
                    case 5: // online
                        distributeStatus = `<span class="badge badge-primary album-status-badge">Online</span>`;
                        statusClass = 'badge-primary';
                        break;
                    default:
                        distributeStatus =
                            `<span class="badge badge-secondary album-status-badge">Unknown Status</span>`;
                        statusClass = 'badge-secondary';
                }

                // Format release date
                const releaseDate = new Date(album.releaseDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                // Distribute button on album image - only show for not distributed albums
                const distributeButton = album.distributed === 0 ?
                    `<button id="btn-quick-dis-${album.id}" class="distribute-button distribute-btn cur-poiter" data-album-id="${album.id}" title="Distribute this album">
                    <i class="fas fa-solid fa-rocket"></i>
                </button>` : '';

                const creatorInfo = `
                <img src="/images/avatar/${album.username}.jpg" class="user-avatar creator-info" title="${album.username}" alt="${album.username}" style="position:absolute;top:10px;left:10px">
            `;
                const coverImgHtml = album.coverImg ? `
                <div class="album-cover-placeholder">
                    <i class="fas fa-music"></i>
                </div>

                <div class="image-loading-spinner" style="display: none;">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </div>

                <img data-src="${album.coverImg}" 
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%23f8f9fa'/%3E%3C/svg%3E"
                     class="card-img-top album-cover lazy-loading" 
                     alt="${album.name}">
            ` : `
                <div class="album-cover-placeholder">
                    <i class="fas fa-music"></i>
                    <div class="mt-2 small text-muted">No Cover</div>
                </div>
            `;

                let albumCard = `
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card album-card" data-album-id="${album.id}">
                <div class="album-cover-wrapper">
                    ${creatorInfo}
                    ${distributeStatus}
                    ${coverImgHtml}
                    ${distributeButton}
                </div>
                <div class="card-body">
                    <h6 class="album-title">${album.name}</h6>
                    
                    <div class="album-meta">
                        <div class="album-meta-item">
                            <i class="fas fa-hashtag"></i> ID: ${album.id}
                        </div>    
                        <div class="album-meta-item">
                            <i class="fas fa-user"></i> ${album.artist}
                                    ${album.artist_youtube_claim == 1 ? '<i class="fas fa-check-circle ml-1" style="color: #17a2b8;" data-toggle="tooltip" title="CID enabled for this artist"></i>' : ''}
                                    ${album.youtube_claim == 1 ? '<i class="fas fa-dollar-sign ml-2" style="color: #28a745;" data-toggle="tooltip" title="CID enabled for this album"></i>' : ''}
                        </div>
                        <div class="album-meta-item">
                            <i class="fas fa-calendar-alt"></i> ${releaseDate}
                        </div>
                        <div class="album-meta-item">
                            <i class="fas fa-music"></i> ${songCount} songs
                        </div>
                    </div>
                    <span class="album-genre">${album.genre}</span>
                        
            </div>
        </div>
    `;
                albumsContainer.append(albumCard);
            });

            // Event handlers
            $('.album-card').click(function(e) {
                if (!$(e.target).hasClass('distribute-btn') && !$(e.target).parent().hasClass('distribute-btn')) {
                    const albumId = $(this).data('album-id');
                    showAlbumDetails(albumId);
                }
            });

            $('.distribute-btn').click(function(e) {
                e.stopPropagation();
                const albumId = $(this).data('album-id');
                distribute(albumId);
            });

            // Setup album search functionality after rendering albums
            setupAlbumSearch();
        }

        function setupReleaseDateEdit() {
            // Use event delegation since the button is added dynamically
            $(document).on('click', '.edit-release-date-btn', function(e) {
                e.preventDefault();

                const albumId = $(this).data('album-id');
                const currentDate = $(this).data('date');

                // Convert date string to acceptable format for input (YYYY-MM-DD)
                const formattedDate = new Date(currentDate).toISOString().split('T')[0];

                // Create date edit modal if it doesn't exist
                if ($('#editReleaseDateModal').length === 0) {
                    $('body').append(`
                    <div class="modal fade" id="editReleaseDateModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div class="d-flex justify-content-sm-between align-items-center w-100">
                                        <div>
                                            <h5 id="editReleaseDateModalLabel" class="modal-title mt-0 d-flex align-items-center">
                                                Edit Release Date
                                            </h5>
                                        </div>
                                        <div class="">

                                            <button
                                                class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                                                data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
                                                style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                                     class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>                                                           
                                <div class="modal-body">
                                    <form id="edit-release-date-form">
                                        <div class="form-group">
                                            <label for="new-release-date">New Release Date</label>
                                            <input type="date" class="form-control" id="new-release-date" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="save-release-date-btn">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                }

                // Set the current date in the input field
                $('#new-release-date').val(formattedDate);

                // Store the album ID for use in the save handler
                $('#editReleaseDateModal').data('album-id', albumId);

                // Show the modal
                $('#editReleaseDateModal').modal('show');
            });

            // Handle save button click
            $(document).on('click', '#save-release-date-btn', function() {
                const albumId = $('#editReleaseDateModal').data('album-id');
                const newDate = $('#new-release-date').val();

                if (!newDate) {
                    showNotification('Please select a valid date', 'warning');
                    return;
                }

                // Show loading state
                const saveBtn = $(this);
                saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

                // Make AJAX call to update the release date
                updateReleaseDate(albumId, newDate)
                    .then(response => {
                        // Close the modal
                        $('#editReleaseDateModal').modal('hide');

                        // Update the UI
                        updateReleaseDateUI(albumId, newDate);

                        // Show success notification
                        showNotification(response.message, response.status);
                        saveBtn.html('Save Changes').prop('disabled', false);
                    })
                    .catch(error => {
                        console.error('Error updating release date:', error);
                        showNotification('Failed to update release date. Please try again.', 'error');
                        saveBtn.html('Save Changes').prop('disabled', false);
                    });
            });
        }

        function updateReleaseDate(albumId, newDate) {
            return $.ajax({
                url: '/updateAlbumReleaseDate',
                type: 'GET',
                data: {
                    album_id: albumId,
                    release_date: newDate
                },
                dataType: 'json'
            });
        }

        function updateReleaseDateUI(albumId, newDate) {
            // Format the date for display
            const formattedDate = new Date(newDate).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Update the displayed date
            $('#release-date-display').text(formattedDate);

            // Update the edit button's data attribute
            $(`.edit-release-date-btn[data-album-id="${albumId}"]`).data('date', newDate);

            // Also update the album object in memory if it exists
            const albumIndex = albums.findIndex(a => a.id === albumId);
            if (albumIndex !== -1) {
                albums[albumIndex].releaseDate = newDate;
            }
        }

        function addReleaseDateEditStyles() {
            if ($('#release-date-edit-styles').length === 0) {
                $('head').append(`
                <style id="release-date-edit-styles">
                    .edit-release-info-btn {
                        background: none;
                        border: none;
                        color: #007bff;
                        font-size: 14px;
                        padding: 0;
                        margin-left: 8px;
                        cursor: pointer;
                        transition: color 0.2s ease;
                    }

                    .edit-release-info-btn:hover {
                        color: #0056b3;
                    }

                    .edit-release-info-btn:focus {
                        outline: none;
                    }

                    .album-info-value {
                        display: flex;
                        align-items: center;
                    }

                    #new-release-date {
                        border-radius: 6px;
                        padding: 8px 12px;
                        border: 1px solid #ced4da;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
                        transition: all 0.2s ease;
                    }

                    #new-release-date:focus {
                        border-color: #80bdff;
                        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                        outline: none;
                    }
                </style>
            `);
            }
        }

        function updateAlbumInfoHTML(album) {
            // Format release date
            const releaseDate = new Date(album.releaseDate).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            let instrumentsHTML = '';
            if (album.instruments && album.instruments.length > 0) {
                instrumentsHTML = `
            <div class="album-info-item">
                <div class="album-info-label">Instruments</div>
                <div class="album-info-value instruments-container">
                    <div class="instruments-list">
                        ${album.instruments.map(instrument =>
                    `<span class="album-genre-badge">${instrument}</span>`
            ).join('')}
                    </div>
                </div>
            </div>
        `;
            }

            let spotifyLinksHTML = '';

            if (album.spotify_info) {
                try {
                    // Parse spotify_info if it's a string
                    const spotifyInfo = typeof album.spotify_info === 'string' ?
                        JSON.parse(album.spotify_info) :
                        album.spotify_info;

                    if (spotifyInfo) {
                        spotifyLinksHTML = `
                    <div class="album-spotify-links">
                        ${spotifyInfo.album_id ? `
                                <div class="album-spotify-link" data-spotify-url="https://open.spotify.com/album/${spotifyInfo.album_id}">
                                    <i class="fab fa-spotify"></i>
                                    <span>Copy Album Link</span>
                                </div>
                            ` : ''}

                        ${spotifyInfo.artist_id ? `
                                <div class="album-spotify-link" data-spotify-url="https://open.spotify.com/artist/${spotifyInfo.artist_id}">
                                    <i class="fas fa-user"></i>
                                    <span>Copy Artist Link</span>
                                </div>
                            ` : ''}
                    </div>
                `;
                    }
                } catch (e) {
                    console.error('Error parsing spotify_info:', e);
                }
            }

            // Generate status badge with appropriate CSS class and icon
            let statusBadgeClass = '';
            let statusIcon = '';
            let statusText = '';

            switch (album.distributed) {
                case 0:
                    statusBadgeClass = 'not-distributed';
                    statusIcon = 'fa-times-circle';
                    statusText = 'Not Distributed';
                    break;
                case 1:
                    statusBadgeClass = 'badge-warning';
                    statusIcon = 'fa-clock';
                    statusText = 'Pending Distribution';
                    break;
                case 2:
                    statusBadgeClass = 'badge-info';
                    statusIcon = 'fa-sync fa-spin';
                    statusText = 'Distributing';
                    break;
                case 3:
                    statusBadgeClass = 'distributed';
                    statusIcon = 'fa-check-circle';
                    statusText = 'Distributed';
                    break;
                case 4:
                    statusBadgeClass = 'badge-danger';
                    statusIcon = 'fa-exclamation-circle';
                    statusText = 'Error';
                    break;
                case 5:
                    statusBadgeClass = 'badge-primary';
                    statusIcon = 'fa-globe';
                    statusText = 'Online';
                    break;
                default:
                    statusBadgeClass = 'not-distributed';
                    statusIcon = 'fa-question-circle';
                    statusText = 'Unknown Status';
            }

            const artistAlbumCount = getArtistAlbumCount(album.artist || 'Various Artists');
            const artistAlbumCountText = artistAlbumCount > 1 ?
                `<span class="artist-album-count">${artistAlbumCount} albums</span>` : '';

            const albumInfoHTML = `


    <div class="album-info-panel">
        <div class="album-creator">
            <img src="/images/avatar/${album.username}.jpg" class="user-avatar" alt="Creator avatar">
            <span>Created by ${album.username}</span>
        </div>

        <div class="album-info-grid">
                            <div class="album-info-item">
                <div class="album-info-label">ID</div>
                <div class="album-info-value">
                    <span class="album-id-badge">#${album.id}</span>
                </div>
            </div>
            <div class="album-info-item">
                <div class="album-info-label">Artist</div>
                <div class="album-info-value">
                    <i class="fas fa-user"></i> 
                    <span id="artist-name-display">${album.artist || 'Various Artists'}</span>
                    ${artistAlbumCountText}    

                </div>
            </div>

            <div class="album-info-item">
                <div class="album-info-label">Release Date</div>
                <div class="album-info-value">
                    <i class="fas fa-calendar-alt"></i> 
                    <span id="release-date-display">${releaseDate}</span>
                    
                    ${album.distributed < 2 ?
                `<button class="edit-release-date-btn edit-release-info-btn ml-2" data-album-id="${album.id}" data-date="${album.releaseDate}">
                                <i class="fas fa-pencil-alt"></i>
                            </button>` : ''}
                </div>
            </div>

            <div class="album-info-item">
                <div class="album-info-label">Genre</div>
                <div class="album-info-value">
                    <span class="album-genre-badge">
                        <i class="fas fa-tag"></i> ${album.genre || 'Various'}
                    </span>
                </div>
            </div>

            ${instrumentsHTML}

            <div class="album-info-item">
                <div class="album-info-label">Songs</div>
                <div class="album-info-value">
                    <i class="fas fa-music"></i> <span id="song-count">${album.songs ? album.songs.length : 0}</span>
                </div>
            </div>

            <div class="album-info-item">
                <div class="album-info-label">Status</div>
                <div class="album-info-value">
                    <span class="detail-status-badge ${statusBadgeClass}" id="current-album-status">
                        <i class="fas ${statusIcon}"></i>
                        ${statusText}
                    </span>
                </div>
            </div>                              
        </div>
        ${spotifyLinksHTML}
    </div>
    `;

            return albumInfoHTML;
        }

        function addArtistChartButton(album) {
            // Check if the album-spotify-links section exists
            if ($('.album-spotify-links').length > 0) {
                // Add the artist stats button after the album-spotify-links
                $('.album-spotify-links').after(`
                <div class="album-artist-stats mt-3">
                    <div class="view-stats-link" id="show-artist-chart" data-artist="${encodeURIComponent(album.artist)}">
                        <i class="fas fa-chart-line" style="color: #007bff;"></i>
                        <span>View Artist Stats</span>
                    </div>
                </div>
            `);
            } else {
                // If spotify links don't exist, add after the album info grid
                $('.album-info-grid').after(`
                <div class="album-artist-stats mt-3">
                    <div class="view-stats-link" id="show-artist-chart" data-artist="${encodeURIComponent(album.artist)}">
                        <i class="fas fa-chart-line" style="color: #007bff;"></i>
                        <span>View Artist Stats</span>
                    </div>
                </div>
            `);
            }

            // Add the chart container at the bottom of the card-body without card wrapper
            $('#album-details .card-body').append(`
            <div class="row">
                <div class="col-12">        
                <div id="artist-chart-container" class="col-md-12 mt-3 album-info-panel" style="display: none;">
                    <div class="chart-header d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line mr-1"></i> ${album.artist} Statistics
                        </h6>
                        <button id="close-artist-chart" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="chart-body">
                        <iframe id="artist-chart-iframe" src="about:blank" width="100%" frameborder="0" style="min-height: 800px;"></iframe>
                    </div>
                </div>
                </div>
            </div>
        `);
        }

        function setupArtistChartHandlers() {
            // Toggle chart visibility when button is clicked
            $(document).off('click', '#show-artist-chart').on('click', '#show-artist-chart', function() {
                const artistName = $(this).data('artist');
                const chartUrl = `https://distro.360promo.fm/iframe/charts/${artistName}`;

                // Update iframe src
                $('#artist-chart-iframe').attr('src', chartUrl);

                // Show chart container
                $('#artist-chart-container').slideDown();

                // Scroll to the chart
                $('html, body').animate({
                    scrollTop: $('#artist-chart-container').offset().top - 100
                }, 500);

                // Change button text
                $(this).find('span').text('Hide Artist Stats');
                $(this).attr('id', 'hide-artist-chart');

                // Listen for messages from the iframe to adjust height if needed
                window.addEventListener('message', function(e) {
                    if (e.data && e.data.type === 'resize' && e.data.height) {
                        $('#artist-chart-iframe').css('height', e.data.height + 'px');
                    }
                });
            });

            // Hide chart when hide button is clicked
            $(document).off('click', '#hide-artist-chart').on('click', '#hide-artist-chart', function() {
                // Hide chart container
                $('#artist-chart-container').slideUp();

                // Change button text back
                $(this).find('span').text('View Artist Stats');
                $(this).attr('id', 'show-artist-chart');
            });

            // Close button functionality
            $(document).off('click', '#close-artist-chart').on('click', '#close-artist-chart', function() {
                // Hide chart container
                $('#artist-chart-container').slideUp();

                // Update button
                $('#hide-artist-chart').find('span').text('View Artist Stats');
                $('#hide-artist-chart').attr('id', 'show-artist-chart');
            });
        }

        function showAlbumDetails(albumId) {
            currentAlbumId = albumId;
            $('#album-details').show();
            $('#albums-container').hide();
            $('#album-details .card-body').html(
                '<div class="text-center my-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 col-md-12">Loading album details...</p></div>'
            );
            fetchAlbumDetails(albumId, function(error, album) {
                if (error) {
                    $('#album-details .card-body').html(
                        '<div class="text-center my-5"><i class="fas fa-exclamation-triangle text-danger fa-2x"></i><p class="mt-2 col-md-12">Failed to load album details. Please try again.</p></div>'
                    );
                    console.error('Error fetching album details:', error);
                    return;
                }
                currentAlbumData = album;
                let distributionIndicator = '';
                if (album.distroReleaseDate) {
                    const distroDate = new Date(album.distroReleaseDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    distributionIndicator = `
                <span class="distribution-indicator ml-2" data-toggle="tooltip" title="Distributed on ${distroDate}">
                    <i class="fas fa-check-circle text-success"></i>
                </span>
            `;
                }

                // Create action buttons based on the current album status
                let actionButtons = '';

                // Only show Spotify scan button if album is fully distributed (3) or online (5)
                if (album.distributed === 3 || album.distributed === 5) {
                    actionButtons += `
                <button id="btn-spotify-scan" class="btn btn-action btn-spotify" data-album-id="${album.id}" onclick="scanSpotify(${album.id})">
                    <i class="fab fa-spotify"></i> Scan
                </button>
            `;
                }

                // Admin can add songs and distribute for statuses 0, 1, 4
                if (album.distributed === 0 || album.distributed === 1 || album.distributed === 4) {
                    actionButtons += `
                @if ($is_admin_music)
                    <button id="album-details-distribute-btn" class="btn btn-action btn-distribute-detail distribute-btn" data-album-id="${album.id}" onclick="distribute(${album.id})">
                        <i class="fas fa-solid fa-rocket"></i> Distribute
                    </button>
                    <button class="btn btn-action btn-add-songs" data-toggle="modal" data-target="#addSongsModal">
                        <i class="fas fa-plus"></i> Add Songs
                    </button>
                @else
                    ${album.distributed === 0 ? `
                            <button id="album-details-distribute-btn" class="btn btn-action btn-distribute-detail distribute-btn" data-album-id="${album.id}" onclick="distribute(${album.id})">
                                <i class="fas fa-solid fa-rocket"></i> Distribute
                            </button>
                            <button class="btn btn-action btn-add-songs" data-toggle="modal" data-target="#addSongsModal">
                                <i class="fas fa-plus"></i> Add Songs
                            </button>
                        ` : ''}
                @endif
            `;
                }

                // For error status, show retry button for both admin and users
                if (album.distributed === 4) {
                    actionButtons += `
                <button id="album-details-retry-btn" class="btn btn-action btn-danger distribute-btn" data-album-id="${album.id}" onclick="distribute(${album.id})">
                    <i class="fas fa-redo"></i> Retry Distribution
                </button>
            `;
                }
                let editButton = album.distributed < 2 ?
                    `<button class="btn btn-link btn-edit-album" title="Edit album" style="padding:0 0 0 8px;">
                                <i class="fas fa-pencil-alt"></i>
                        </button>` : '';

                const headerContent = `
                <div class="album-detail-title">
                    <button class="btn btn-back" id="back-to-albums">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="header-album-info">
                        <img src="${album.coverImg}" class="header-album-cover" alt="${album.name}">
                        <h5 id="current-album-name">
                            ${album.name}
                            ${distributionIndicator}
                            ${editButton}
                        </h5>
                    </div>
                </div>
                <div class="album-action-buttons">
                    ${actionButtons}
                </div>
            `;

                // Update header content
                $('.card-header .d-flex').html(headerContent);
                $('[data-toggle="tooltip"]').tooltip();

                // Update card body content
                const albumInfoColumn = $('<div class="col-md-4 mb-4 album-info-container"></div>');
                albumInfoColumn.html(updateAlbumInfoHTML(album));

                const songsColumn = $('<div class="col-md-8 songs-container"></div>');
                songsColumn.html(`
            <div class="songs-header-panel">
                <h5 class="mb-0">Songs List</h5>
                <form id="album-songs-search-form" class="search-container">
                    <div class="search-container" id="album-search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Search and press Enter..." id="album-songs-search">
                    </div>
                </form>
            </div>
                                                                                                                    
            <div class="songs-content-panel">                                                                                                        
            <div class="songs-list" id="album-songs-list">
            </div>
                
            <div class="audio-player mt-4 d-none" id="audio-player">
                <div class="player-controls mb-2">
                    <button class="btn btn-play" id="player-play-btn">
                        <i class="fas fa-play"></i>
                    </button>
                    <div class="player-song-info">
                        <div class="font-weight-bold" id="player-song-title">Song Title</div>
                        <small class="text-muted" id="player-song-artist">Artist Name</small>
                    </div>
                    <span class="text-muted" id="player-time">0:00 / 0:00</span>
                </div>
                <div class="progress">
                    <div id="player-progress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                </div>
                <audio id="audio-element" src=""></audio>
            </div>
            </div>
        `);

                // Update content
                $('#album-details .card-body').empty()
                    .append($('<div class="row h-100"></div>').append(albumInfoColumn).append(songsColumn));

                // Render songs list
                renderAlbumSongs(album);

                // Add artist chart button for distributed or online albums
                if ((album.distributed === 3 || album.distributed === 5) && album.distroReleaseDate) {
                    addArtistChartButton(album);
                    setupArtistChartHandlers();
                }

                // Set up Back button event
                $('#back-to-albums').off('click').on('click', function() {
                    // Hide album details and show albums list
                    $('#album-details').hide();
                    $('#albums-container').show();
                    currentAlbumId = null;

                    // Stop playback if playing
                    const audioElement = $('#audio-element')[0];
                    if (audioElement) {
                        audioElement.pause();
                        $('#audio-player').addClass('d-none');
                    }
                });

                // Setup song search
                setupAlbumSongSearch();
            });
        }

        function renderAlbumSongs(album) {
            const songsContainer = $('#album-songs-list');
            songsContainer.empty();

            // Only enable drag-n-drop for non-distributed albums (status 0, 1, and 4)distribute
            const canEdit = album.distributed === 0 || album.distributed === 1 || album.distributed === 4;

            // Add class to songs container if album is distributed
            songsContainer.parent().toggleClass('album-distributed', album.distributed === 3 || album.distributed === 5);

            if (!album.songs || !album.songs.length) {
                songsContainer.html(`
                <div class="text-center py-5 text-muted" id="empty-album-message">
                    <i class="fas fa-music fa-3x mb-3"></i>
                    <p class="w-100">This album has no songs yet</p>
                    ${canEdit ?
                    `<button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#addSongsModal">
                                Add Songs Now
                            </button>` : ''}
                </div>
            `);
                $('#audio-player').addClass('d-none');
                return;
            }

            // Sort songs by order_id if available
            let sortedSongs = [...album.songs];
            sortedSongs.sort((a, b) => {
                if (a.order_id && b.order_id) {
                    return a.order_id - b.order_id;
                }
                return 0; // Keep original order if order_id is not available
            });

            sortedSongs.forEach(song => {
                const spotifyButton = song.spotify_id ?
                    `<div class="spotify-link mr-2 cur-poiter" title="Copy Spotify link" data-spotify-id="${song.spotify_id}">
                    <i class="fab fa-spotify"></i>
                </div>` : '';

                // Add drag handle - only shown if album can be edited
                const dragHandle = canEdit ?
                    `<div class="drag-handle mr-2">
                    <i class="fas fa-grip-vertical"></i>
                </div>` : '';

                const songItem = `
                <div class="song-item d-flex justify-content-between align-items-center searchable-song-item" data-song-id="${song.id}" data-order="${song.order_id || 0}">
                    <div class="d-flex align-items-center">
                        ${dragHandle}
                        <button class="btn-play-song play-song-btn mr-2" data-song-id="${song.id}" type="button">
                            <i class="fas fa-play"></i>
                        </button>
                        <div>
                            <div class="song-title searchable-title">${song.title}</div>
                            <div class="song-artist searchable-artist">${song.artist}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        ${spotifyButton}        
                        <span class="song-duration mr-3">${song.duration || '00:00'}</span>
                        ${canEdit ?
                    `<button class="btn btn-sm btn-outline-danger remove-song-btn btn-remove-song" data-song-id="${song.id}" title="Remove song" type="button">
                                    <i class="fas fa-times"></i>
                                </button>` : ''}
                    </div>
                </div>
            `;
                songsContainer.append(songItem);
            });

            // Add click event to play song buttons
            $('.play-song-btn').click(function(e) {
                e.stopPropagation();
                const songId = $(this).data('song-id');
                const song = album.songs.find(s => s.id == songId); // Use == instead of === for type coercion
                if (song) {
                    playSong(song);
                }
            });

            // Only add click events for remove buttons if album can be edited
            if (canEdit) {
                // Add click event to remove song buttons
                $('.remove-song-btn').click(function(e) {
                    e.stopPropagation();
                    const songId = $(this).data('song-id');
                    handleRemoveSongFromAlbum(songId, album.id);
                });

                // Initialize sortable for editable albums
                initSortable();
            }

            $('.spotify-link').click(function(e) {
                e.stopPropagation();
                const spotifyId = $(this).data('spotify-id');
                const spotifyUrl = `https://open.spotify.com/track/${spotifyId}`;

                // Copy to clipboard
                copyToClipboard2(spotifyUrl);

                // Show copied effect
                showCopiedEffect($(this));

                // Optional: show notification
                showNotification("Spotify link copied to clipboard", "success");
            });
        }

        function initSortable() {
            const songsList = document.getElementById('album-songs-list');

            if (songsList && typeof Sortable !== 'undefined') {
                // Create a Sortable instance for the songs list
                const sortable = new Sortable(songsList, {
                    animation: 150,
                    ghostClass: 'song-item-ghost',
                    chosenClass: 'song-item-chosen',
                    dragClass: 'song-item-drag',
                    handle: '.drag-handle',
                    onEnd: function(evt) {
                        // When dragging ends, save the new order
                        saveSongOrder();
                    }
                });
            } else {
                console.warn(
                'Could not initialize Sortable.js. Make sure the library is loaded and the songs list exists.');
            }
        }

        function saveSongOrder() {
            // Get all song items
            const songItems = document.querySelectorAll('#album-songs-list .song-item');
            const albumId = currentAlbumId;

            if (!albumId || songItems.length === 0) {
                return;
            }

            // Create an array of song IDs in the new order
            const songOrder = [];
            songItems.forEach((item, index) => {
                const songId = item.getAttribute('data-song-id');
                songOrder.push({
                    song_id: songId,
                    order_id: index + 1 // Order starts from 1
                });
            });

            // Show saving indication
            //    showNotification('Saving song order...', 'info');

            // Send the new order to the server
            $.ajax({
                url: '/updateSongOrder',
                type: 'POST',
                data: {
                    album_id: albumId,
                    song_order: JSON.stringify(songOrder),
                    _token: $('input[name="_token"]').attr('value')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('Song order updated successfully', 'success');

                        // Update the order_id in the local album object
                        if (albums && albums.length) {
                            const albumIndex = albums.findIndex(a => a.id === parseInt(albumId));
                            if (albumIndex !== -1 && albums[albumIndex].songs) {
                                songOrder.forEach(orderItem => {
                                    const songIndex = albums[albumIndex].songs.findIndex(
                                        s => s.id == orderItem.song_id
                                    );
                                    if (songIndex !== -1) {
                                        albums[albumIndex].songs[songIndex].order_id = orderItem
                                            .order_id;
                                    }
                                });
                            }
                        }
                    } else {
                        showNotification('Failed to update song order: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating song order:', error);
                    showNotification('Failed to update song order. Please try again.', 'error');
                }
            });
        }

        function setupAlbumSongSearch() {

            // Xóa tất cả sự kiện cũ
            $('#album-songs-search').off();

            // Thêm sự kiện submit cho form search
            $('#album-songs-search-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                performAlbumSongSearch();
            });

            // Thêm sự kiện keypress để bắt phím Enter
            $('#album-songs-search').off('keypress').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    performAlbumSongSearch();
                }
            });

            // Thêm nút clear search
            $('#album-songs-search-clear').off('click').on('click', function() {
                $('#album-songs-search').val('');
                $('#album-songs-list .song-item').show();
            });
        }

        function playSong(song) {

            if (!song || !song.audioUrl) {
                console.error('Cannot play song: Invalid song data or missing audio URL');
                showNotification('Cannot play this song. Audio URL is missing.', 'warning');
                return;
            }

            const audioElement = $('#audio-element')[0];

            if (currentAudio && currentAudio.id === song.id) {
                if (!audioElement.paused) {
                    audioElement.pause();
                    $('#player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                    $(`.play-song-btn[data-song-id="${song.id}"]`).html('<i class="fas fa-play"></i>').removeClass(
                        'playing');
                    return;
                }
                // Nếu đang tạm dừng, tiếp tục phát
                else {
                    audioElement.play();
                    $('#player-play-btn').html('<i class="fas fa-pause"></i>').addClass('playing');
                    $(`.play-song-btn[data-song-id="${song.id}"]`).html('<i class="fas fa-pause"></i>').addClass('playing');
                    return;
                }
            }


            $('.play-song-btn').html('<i class="fas fa-play"></i>').removeClass('playing');

            // Cập nhật thông tin bài hát trong player
            $('#player-song-title').text(song.title || 'Unknown Title');
            $('#player-song-artist').text(song.artist || 'Unknown Artist');

            // Cập nhật src của audio element
            audioElement.src = song.audioUrl;
            audioElement.load();

            // Hiển thị audio player
            $('#audio-player').removeClass('d-none');

            // Cập nhật nút play của bài hát
            const songButton = $(`.play-song-btn[data-song-id="${song.id}"]`);
            songButton.html('<i class="fas fa-pause"></i>').addClass('playing');

            // Phát nhạc
            audioElement.play().catch(error => {
                console.error('Error playing audio:', error);
                showNotification('Error to play audio. Please try again.', 'error');
            });

            // Lưu thông tin bài hát hiện tại
            currentAudio = song;

            // Cập nhật nút play của player
            $('#player-play-btn').html('<i class="fas fa-pause"></i>').addClass('playing');

            // Nếu đang phát nhạc trong modal, dừng lại
            const modalAudioElement = $('#modal-audio-element')[0];
            if (modalAudioElement && !modalAudioElement.paused) {
                modalAudioElement.pause();
                $('#modal-player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                $('.modal-play-song-btn.playing').html('<i class="fas fa-play"></i>').removeClass('playing');
            }

            // Reset progress bar
            $('#player-progress').css('width', '0%');
            $('#player-time').text('0:00 / 0:00');

            // Make sure timeupdate event is attached
            $(audioElement).off('timeupdate').on('timeupdate', updateMainAudioProgress);
        }

        function updateMainAudioProgress() {
            const audioElement = this;

            if (!audioElement.duration)
                return;

            const progress = (audioElement.currentTime / audioElement.duration) * 100;
            $('#player-progress').css('width', `${progress}%`);

            // Format times
            const currentMinutes = Math.floor(audioElement.currentTime / 60);
            const currentSeconds = Math.floor(audioElement.currentTime % 60);
            const durationMinutes = Math.floor(audioElement.duration / 60);
            const durationSeconds = Math.floor(audioElement.duration % 60);

            const formattedCurrentTime = `${currentMinutes}:${currentSeconds < 10 ? '0' : ''}${currentSeconds}`;
            const formattedDuration = `${durationMinutes}:${durationSeconds < 10 ? '0' : ''}${durationSeconds}`;

            $('#player-time').text(`${formattedCurrentTime} / ${formattedDuration}`);
        }

        function togglePlayPause() {
            const audioElement = $('#audio-element')[0];

            if (audioElement.paused) {
                audioElement.play();
                $('#player-play-btn').html('<i class="fas fa-pause"></i>').addClass('playing');
                if (currentAudio) {
                    $(`.play-song-btn[data-song-id="${currentAudio}"]`).html('<i class="fas fa-pause"></i>').addClass(
                        'playing');
                }
            } else {
                audioElement.pause();
                $('#player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                if (currentAudio) {
                    $(`.play-song-btn[data-song-id="${currentAudio}"]`).html('<i class="fas fa-play"></i>').removeClass(
                        'playing');
                }
            }
        }

        function updateAudioProgress() {
            const audioElement = $('#audio-element')[0];

            if (!audioElement.duration)
                return;


            const progress = (audioElement.currentTime / audioElement.duration) * 100;
            $('#player-progress').css('width', `${progress}%`);

            // Format times
            const currentMinutes = Math.floor(audioElement.currentTime / 60);
            const currentSeconds = Math.floor(audioElement.currentTime % 60);
            const durationMinutes = Math.floor(audioElement.duration / 60);
            const durationSeconds = Math.floor(audioElement.duration % 60);

            const formattedCurrentTime = `${currentMinutes}:${currentSeconds < 10 ? '0' : ''}${currentSeconds}`;
            const formattedDuration = `${durationMinutes}:${durationSeconds < 10 ? '0' : ''}${durationSeconds}`;

            $('#player-time').text(`${formattedCurrentTime} / ${formattedDuration}`);
        }

        function renderAvailableSongs(groupId = null) {
            const songsContainer = $('#available-songs-list');
            songsContainer.html(
                '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading songs...</div>');
            selectedSongs = [];
            updateSelectedCount();

            // Store the current filters for later use
            window.currentFilters = {
                groupId: groupId || '',
                genre: $('.genre-filter-btn.active').data('genre') || 'all',
                searchTerm: $('#modal-song-search').val() || ''
            };

            // Lấy danh sách bài hát từ API với filter group nếu có
            fetchAvailableSongs(groupId)
                .then(response => {
                    songsContainer.empty();

                    if (!response || !Array.isArray(response) || !response.length) {
                        songsContainer.html('<div class="text-center py-4 text-muted">No available songs</div>');
                        return;
                    }

                    // Lọc ra các bài hát chưa được thêm vào album hiện tại
                    const currentAlbum = albums.find(a => a.id === currentAlbumId);
                    const currentSongIds = currentAlbum && currentAlbum.songs ? currentAlbum.songs.map(s => s.id) : [];

                    const availableSongs = response.filter(song => !currentSongIds.includes(song.id) && !currentSongIds
                        .includes(Number(song.id)));

                    if (!availableSongs.length) {
                        songsContainer.html(
                            '<div class="text-center py-4 text-muted">All songs have been added to this album</div>'
                        );
                        return;
                    }

                    // Tạo danh sách thể loại duy nhất từ các bài hát
                    const genres = [...new Set(availableSongs.map(song => song.genre || 'Unknown'))];

                    // Cập nhật danh sách bộ lọc thể loại
                    const genreFiltersContainer = $('#genre-filters');
                    genreFiltersContainer.find('button:not(:first-child)').remove(); // Giữ lại nút "All Genres"

                    genres.forEach(genre => {
                        if (genre && genre !== 'Unknown') {
                            genreFiltersContainer.append(`
                    <button class="genre-filter-btn" data-genre="${genre}">${genre}</button>
                `);
                        }
                    });

                    // Render các bài hát
                    availableSongs.forEach(song => {
                        const songItem = `
                <div class="modal-song-item d-flex justify-content-between align-items-center" data-song-id="${song.id}" data-genre="${song.genre || 'Unknown'}">
                    <div>
                        <div class="font-weight-bold modal-song-title">${song.title || 'Unknown Title'}</div>
                        <small class="text-muted modal-song-artist">
                            ${song.artist || 'Unknown Artist'} 
                            <span class="badge badge-light">${song.genre || 'Unknown Genre'}</span>
                        </small>
                    </div>
                    <div class="modal-song-controls">
                        <button class="btn btn-play modal-play-song-btn mr-3" data-song-id="${song.id}">
                            <i class="fas fa-play"></i>
                        </button>
                        <span class="text-muted">${song.duration || '00:00'}</span>
                    </div>
                </div>
            `;
                        songsContainer.append(songItem);
                    });

                    // Add click event to select songs
                    $('#available-songs-list .modal-song-item').click(function(e) {
                        // Ignore if clicked on play button
                        if ($(e.target).hasClass('modal-play-song-btn') || $(e.target).parent().hasClass(
                                'modal-play-song-btn')) {
                            return;
                        }

                        const songId = parseInt($(this).data('song-id'));
                        $(this).toggleClass('selected');

                        if ($(this).hasClass('selected')) {
                            selectedSongs.push(songId);
                        } else {
                            selectedSongs = selectedSongs.filter(id => id !== songId);
                        }

                        updateSelectedCount();
                    });

                    // Add click event to play song buttons
                    $('.modal-play-song-btn').click(function(e) {
                        e.stopPropagation(); // Prevent selecting the song

                        const songId = $(this).data('song-id');
                        const song = availableSongs.find(s => s.id == songId);

                        if (song) {
                            playModalSong(song);
                        }
                    });

                    // Set up search functionality
                    setupModalSongSearch();

                    // Set up genre filters - make sure we keep the current filter if there is one
                    setupGenreFilters();
                    if (window.currentFilters && window.currentFilters.genre) {
                        $(`.genre-filter-btn[data-genre="${window.currentFilters.genre}"]`).click();
                    }

                    // Apply search if there was one
                    if (window.currentFilters && window.currentFilters.searchTerm) {
                        $('#modal-song-search').val(window.currentFilters.searchTerm);
                        performModalSongSearch();
                    }
                })
                .catch(error => {
                    console.error('Error fetching available songs:', error);
                    songsContainer.html(
                        '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Failed to load songs</div>'
                    );
                });
        }

        function setupModalSongSearch() {

            // Xóa tất cả sự kiện cũ
            $('#modal-song-search').off();
            $('#modal-songs-search-form').off();

            // Thêm sự kiện submit cho form search
            $('#modal-songs-search-form').on('submit', function(e) {
                e.preventDefault();
                performModalSongSearch();
            });

            // Thêm sự kiện cho nút clear search
            $('#modal-song-search-clear').on('click', function() {
                $('#modal-song-search').val('');
                applyGenreFilter(); // Chỉ áp dụng bộ lọc thể loại sau khi xóa tìm kiếm
            });

            // Đảm bảo input có sự kiện focus và blur
            $('#modal-song-search').on('focus', function() {
                //                console.log('Modal search input focused');
            });

            $('#modal-song-search').on('blur', function() {
                //                console.log('Modal search input blurred');
            });
        }

        function applyGenreFilter() {
            const selectedGenre = $('.genre-filter-btn.active').data('genre');
            //            console.log('Applying genre filter:', selectedGenre);

            let hasResults = false;

            $('.modal-song-item').each(function() {
                const songGenre = $(this).data('genre');

                if (selectedGenre === 'all' || songGenre === selectedGenre) {
                    $(this).removeClass('d-none').addClass('d-flex');
                    hasResults = true;
                } else {
                    $(this).removeClass('d-flex').addClass('d-none');
                }
            });

            // Hiển thị thông báo nếu không có kết quả
            if (!hasResults) {
                showNoResultsMessage('#available-songs-list', 'No songs match the selected genre. Try a different genre.');
            } else {
                hideNoResultsMessage('#available-songs-list');
            }
        }

        function setupGenreFilters() {
            $('#genre-filters').off('click', '.genre-filter-btn').on('click', '.genre-filter-btn', function() {
                $('.genre-filter-btn').removeClass('active');
                $(this).addClass('active');

                // Nếu đang tìm kiếm, áp dụng cả từ khóa tìm kiếm và bộ lọc thể loại
                const searchTerm = $('#modal-song-search').val().trim();
                if (searchTerm) {
                    performModalSongSearch();
                } else {
                    applyGenreFilter();
                }
            });
        }

        $('.modal-song-item').each(function() {
            const songTitle = $(this).find('.modal-song-title').text().toLowerCase();
            const songArtist = $(this).find('.modal-song-artist').text().toLowerCase();
            const songGenre = $(this).data('genre');
            console.log(`Checking song: "${songTitle}" by "${songArtist}", genre: ${songGenre}`);
            const matchesSearch = songTitle.includes(searchTerm) || songArtist.includes(searchTerm);
            const matchesGenre = selectedGenre === 'all' || songGenre === selectedGenre;

            if (matchesSearch && matchesGenre) {
                $(this).removeClass('d-none').addClass('d-flex');
                hasResults = true;
            } else {
                $(this).removeClass('d-flex').addClass('d-none');
            }
        });

        function setupAddSongsModal() {
            // Cập nhật cấu trúc modal
            updateAddSongsModal();

            // Setup group filter functionality
            setupGroupFilter();

            // Khi modal được mở
            $('#addSongsModal').on('show.bs.modal', function() {
                // Dừng nhạc ở màn hình detail nếu đang phát
                const mainAudioElement = $('#audio-element')[0];
                if (mainAudioElement && !mainAudioElement.paused) {
                    mainAudioElement.pause();
                    $('#player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                    $('.play-song-btn.playing').html('<i class="fas fa-play"></i>').removeClass('playing');
                }

            });

            // Khi modal đóng, dừng phát nhạc và reset các bộ lọc
            $('#addSongsModal').on('hidden.bs.modal', function() {
                // Dừng nhạc nếu đang phát
                const modalAudioElement = $('#modal-audio-element')[0];
                if (modalAudioElement) {
                    modalAudioElement.pause();
                }
                $('#modal-audio-player').addClass('d-none');
                $('.modal-play-song-btn.playing').html('<i class="fas fa-play"></i>').removeClass('playing');

                // Reset trạng thái chọn
                selectedSongs = [];
                updateSelectedCount();
            });

            // Sự kiện cho nút play/pause trong modal
            $('#modal-player-play-btn').click(function() {
                const audioElement = $('#modal-audio-element')[0];

                if (audioElement.paused) {
                    audioElement.play();
                    $(this).html('<i class="fas fa-pause"></i>').addClass('playing');
                    if (currentModalAudio) {
                        $(`.modal-play-song-btn[data-song-id="${currentModalAudio.id}"]`).html(
                            '<i class="fas fa-pause"></i>').addClass('playing');
                    }
                } else {
                    audioElement.pause();
                    $(this).html('<i class="fas fa-play"></i>').removeClass('playing');
                    if (currentModalAudio) {
                        $(`.modal-play-song-btn[data-song-id="${currentModalAudio.id}"]`).html(
                            '<i class="fas fa-play"></i>').removeClass('playing');
                    }
                }
            });

            // Sự kiện cập nhật thời gian phát nhạc trong modal
            $('#modal-audio-element').off('timeupdate').on('timeupdate', function() {
                const audioElement = this;

                if (!audioElement.duration)
                    return;

                const progress = (audioElement.currentTime / audioElement.duration) * 100;
                $('#modal-player-progress').css('width', `${progress}%`);

                // Format times
                const currentMinutes = Math.floor(audioElement.currentTime / 60);
                const currentSeconds = Math.floor(audioElement.currentTime % 60);
                const durationMinutes = Math.floor(audioElement.duration / 60);
                const durationSeconds = Math.floor(audioElement.duration % 60);

                const formattedCurrentTime = `${currentMinutes}:${currentSeconds < 10 ? '0' : ''}${currentSeconds}`;
                const formattedDuration = `${durationMinutes}:${durationSeconds < 10 ? '0' : ''}${durationSeconds}`;

                $('#modal-player-time').text(`${formattedCurrentTime} / ${formattedDuration}`);
            });

            // Sự kiện khi bài hát kết thúc
            $('#modal-audio-element').on('ended', function() {
                $('#modal-player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                $('.modal-play-song-btn.playing').html('<i class="fas fa-play"></i>').removeClass('playing');
                currentModalAudio = null;
            });
        }

        function updateSelectedCount() {
            $('#selected-count').text(selectedSongs.length);
        }

        function addSongsToAlbum() {
            if (selectedSongs.length === 0 || !currentAlbumId)
                return;

            const albumIndex = albums.findIndex(a => a.id === currentAlbumId);

            if (albumIndex === -1)
                return;

            selectedSongs.forEach(songId => {
                const songIndex = songs.findIndex(s => s.id === songId);
                if (songIndex !== -1) {
                    songs[songIndex].albumId = currentAlbumId;
                    if (!albums[albumIndex].songs.includes(songId)) {
                        albums[albumIndex].songs.push(songId);
                    }
                }
            });

            $('#addSongsModal').modal('hide');
            renderAlbumSongs(currentAlbumId);
            $('#song-count').text(albums[albumIndex].songs.length);

            if (albums[albumIndex].songs.length > 0) {
                $('#empty-album-message').hide();
            }

            // Update album cards to reflect new song count
            renderAlbums();
        }

        function initializeSearchFunctionality() {
            // Album songs search functionality - cập nhật cho cấu trúc HTML mới
            $('#album-songs-search').off('input').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();

                $('#album-songs-list .song-item').each(function() {
                    const songItem = $(this);
                    const songTitle = songItem.find('.song-title').text().toLowerCase();
                    const songArtist = songItem.find('.song-artist').text().toLowerCase();

                    if (songTitle.includes(searchTerm) || songArtist.includes(searchTerm)) {
                        songItem.show();
                    } else {
                        songItem.hide();
                    }
                });
            });
        }

        function playModalSong(song) {
            if (!song || !song.audioUrl) {
                console.error('Cannot play song: Invalid song data or missing audio URL');
                showNotification('Cannot play this song. Audio URL is missing.', 'warning');
                return;
            }

            const modalAudioElement = $('#modal-audio-element')[0];

            // Kiểm tra xem bài hát hiện tại có phải là bài hát đang được yêu cầu phát không
            if (currentModalAudio && currentModalAudio.id === song.id) {
                // Nếu đang phát, tạm dừng
                if (!modalAudioElement.paused) {
                    modalAudioElement.pause();
                    $('#modal-player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                    $(`.modal-play-song-btn[data-song-id="${song.id}"]`).html('<i class="fas fa-play"></i>').removeClass(
                        'playing');
                    return;
                }
                // Nếu đang tạm dừng, tiếp tục phát
                else {
                    modalAudioElement.play();
                    $('#modal-player-play-btn').html('<i class="fas fa-pause"></i>').addClass('playing');
                    $(`.modal-play-song-btn[data-song-id="${song.id}"]`).html('<i class="fas fa-pause"></i>').addClass(
                        'playing');
                    return;
                }
            }

            // Nếu là bài hát mới, dừng bài hát hiện tại nếu có
            $('.modal-play-song-btn').html('<i class="fas fa-play"></i>').removeClass('playing');

            // Cập nhật thông tin bài hát trong player
            $('#modal-player-song-title').text(song.title || 'Unknown Title');
            $('#modal-player-song-artist').text(song.artist || 'Unknown Artist');

            // Cập nhật src của audio element
            modalAudioElement.src = song.audioUrl;
            modalAudioElement.load();

            // Hiển thị audio player
            $('#modal-audio-player').removeClass('d-none');

            // Cập nhật nút play của bài hát
            const songButton = $(`.modal-play-song-btn[data-song-id="${song.id}"]`);
            songButton.html('<i class="fas fa-pause"></i>').addClass('playing');

            // Phát nhạc
            modalAudioElement.play().catch(error => {
                console.error('Error playing audio:', error);
                showNotification('Failed to play audio. Please try again.', 'error');
            });

            // Lưu thông tin bài hát hiện tại
            currentModalAudio = song;

            // Cập nhật nút play của player
            $('#modal-player-play-btn').html('<i class="fas fa-pause"></i>').addClass('playing');

            // Nếu đang phát nhạc trong trang chi tiết album, dừng lại
            const mainAudioElement = $('#audio-element')[0];
            if (mainAudioElement && !mainAudioElement.paused) {
                mainAudioElement.pause();
                $('#player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                $('.play-song-btn.playing').html('<i class="fas fa-play"></i>').removeClass('playing');
            }
        }

        function updateAddSongsModal() {
            $('#addSongsModal .modal-dialog').addClass('modal-lg');
            $('#addSongsModal .modal-body').html(`
    <div class="modal-filter-container">
        
        <div class="form-group mb-3">
            <h6 class="mb-2">Filter by Group</h6>
            <select id="group-filter" class="form-control" data-show-subtext="true" data-live-search="true" data-size="5" data-container="body">
                <option value="-1">All Groups</option>
                    
            </select>
        </div>
        
        <h6 class="mb-2">Filter by Genre</h6>
        <div id="genre-filters" class="mb-2">
            <button class="genre-filter-btn active" data-genre="all">All Genres</button>
           
        </div>
    </div>
    <form id="modal-songs-search-form" class="form-group search-container mb-3">
        <i class="fas fa-search search-icon"></i>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search songs and press Enter..." id="modal-song-search">
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" id="modal-song-search-clear">
                    <i class="fas fa-times"></i>
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </form>
    <div class="modal-songs-list" id="available-songs-list">
        
    </div>
        
   
    <div class="audio-player mt-3 d-none" id="modal-audio-player">
        <div class="player-controls mb-2">
            <button class="btn btn-play" id="modal-player-play-btn">
                <i class="fas fa-play"></i>
            </button>
            <div class="player-song-info">
                <div class="font-weight-bold" id="modal-player-song-title">Song Title</div>
                <small class="text-muted" id="modal-player-song-artist">Artist Name</small>
            </div>
            <span class="text-muted" id="modal-player-time">0:00 / 0:00</span>
        </div>
        <div class="progress">
            <div id="modal-player-progress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
        </div>
        <audio id="modal-audio-element" src=""></audio>
    </div>
`);
        }

        function setupProgressBarDrag() {
            $('.progress').off('click');

            $(document).on('click', '.progress', function(e) {
                e.preventDefault();
                const progressBar = $(this);
                const audioElement = progressBar.closest('.audio-player').find('audio')[0];

                if (!audioElement || !audioElement.duration)
                    return;

                const offset = e.pageX - progressBar.offset().left;
                const percent = offset / progressBar.width();
                const seekTime = percent * audioElement.duration;

                audioElement.currentTime = seekTime;
            });

            $(document).off('click', '#player-play-btn').on('click', '#player-play-btn', function() {
                console.log('Play button clicked');
                const audioElement = $('#audio-element')[0];

                if (!audioElement) {
                    console.error('Audio element not found');
                    return;
                }

                if (audioElement.paused) {
                    audioElement.play()
                        .then(() => {
                            $(this).html('<i class="fas fa-pause"></i>').addClass('playing');
                            if (currentAudio) {
                                $(`.play-song-btn[data-song-id="${currentAudio.id}"]`).html(
                                    '<i class="fas fa-pause"></i>').addClass('playing');
                            }
                        })
                        .catch(error => {
                            console.error('Failed to play audio:', error);
                            showNotification('Failed to play audio. Please try again.', 'error');
                        });
                } else {
                    audioElement.pause();
                    $(this).html('<i class="fas fa-play"></i>').removeClass('playing');
                    if (currentAudio) {
                        $(`.play-song-btn[data-song-id="${currentAudio.id}"]`).html('<i class="fas fa-play"></i>')
                            .removeClass('playing');
                    }
                }
            });

            $(document).off('click', '#modal-player-play-btn').on('click', '#modal-player-play-btn', function() {
                console.log('Modal play button clicked');
                const audioElement = $('#modal-audio-element')[0];

                if (!audioElement) {
                    console.error('Modal audio element not found');
                    return;
                }

                if (audioElement.paused) {
                    audioElement.play();
                    $(this).html('<i class="fas fa-pause"></i>').addClass('playing');
                    if (currentModalAudio) {
                        $(`.modal-play-song-btn[data-song-id="${currentModalAudio.id}"]`).html(
                            '<i class="fas fa-pause"></i>').addClass('playing');
                    }
                } else {
                    audioElement.pause();
                    $(this).html('<i class="fas fa-play"></i>').removeClass('playing');
                    if (currentModalAudio) {
                        $(`.modal-play-song-btn[data-song-id="${currentModalAudio.id}"]`).html(
                            '<i class="fas fa-play"></i>').removeClass('playing');
                    }
                }
            });
        }

        function showNoResultsMessage(container, message) {
            if ($(container).find('.no-results-message').length === 0) {
                $(container).append(`<div class="no-results-message">${message}</div>`);
            }
        }

        function hideNoResultsMessage(container) {
            $(container).find('.no-results-message').remove();
        }

        function performAlbumSongSearch() {
            const searchTerm = $('#album-songs-search').val().toLowerCase().trim();
            if (!searchTerm) {
                $('#album-songs-list .song-item').removeClass('d-none').addClass('d-flex');
                hideNoResultsMessage('#album-songs-list');
                return;
            }

            let hasResults = false;

            $('#album-songs-list .song-item').each(function() {
                const songTitle = $(this).find('.song-title').text().toLowerCase();
                const songArtist = $(this).find('.song-artist').text().toLowerCase();
                if (songTitle.includes(searchTerm) || songArtist.includes(searchTerm)) {
                    $(this).removeClass('d-none').addClass('d-flex');
                    hasResults = true;
                } else {
                    $(this).removeClass('d-flex').addClass('d-none');
                }
            });

            if (!hasResults) {
                showNoResultsMessage('#album-songs-list', 'No songs match your search. Try different keywords.');
            } else {
                hideNoResultsMessage('#album-songs-list');
            }
        }

        function performModalSongSearch() {
            const searchTerm = $('#modal-song-search').val().toLowerCase().trim();
            if (!searchTerm) {
                applyGenreFilter();
                hideNoResultsMessage('#available-songs-list');
                return;
            }


            const selectedGenre = $('.genre-filter-btn.active').data('genre');
            let hasResults = false;


            $('.modal-song-item').each(function() {
                const songTitle = $(this).find('.modal-song-title').text().toLowerCase();
                const songArtist = $(this).find('.modal-song-artist').text().toLowerCase();
                const songGenre = $(this).data('genre');


                const matchesSearch = songTitle.includes(searchTerm) || songArtist.includes(searchTerm);
                const matchesGenre = selectedGenre === 'all' || songGenre === selectedGenre;

                if (matchesSearch && matchesGenre) {
                    $(this).removeClass('d-none').addClass('d-flex');
                    hasResults = true;
                } else {
                    $(this).removeClass('d-flex').addClass('d-none');
                }
            });

            if (!hasResults) {
                showNoResultsMessage('#available-songs-list',
                    'No songs match your search. Try different keywords or filters.');
            } else {
                hideNoResultsMessage('#available-songs-list');
            }
        }

        function distribute(id) {
            $("#album-details-distribute-btn").html(`<i class="fas fa-spinner fa-spin"></i> Loading...`);
            $(`#btn-quick-dis-${id}`).html(`<i class="fas fa-spinner fa-spin"></i>`);
            $.ajax({
                type: "GET",
                url: "/sendAlbumToSalad",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    logger('distribute', data);
                    $("#album-details-distribute-btn").html(
                        `<i class="fas fa-solid fa-rocket"></i> Distribute`);
                    $(`#btn-quick-dis-${id}`).html(`<i class="fas fa-solid fa-rocket"></i>`);
                    showNotification(data.message, data.status);
                    if (data.status == "success") {
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    $("#album-details-distribute-btn").html(
                    `<i class="fas fa-solid fa-rocket"></i> Distribute`);
                    $(`#btn-quick-dis-${id}`).html(`<i class="fas fa-solid fa-rocket"></i>`);
                    showNotification("Error distributing album. Please try again.", "error");
                }
            });
        }

        function scanSpotify(albumId) {
            const scanButton = $('#btn-spotify-scan');
            scanButton.html('<i class="fas fa-spinner fa-spin"></i> Scanning... 0%');
            scanButton.addClass('scanning');

            if (window.spotifyScanEventSource) {
                window.spotifyScanEventSource.close();
            }

            const eventSource = new EventSource(`/scanAlbum?id=${albumId}`);
            window.spotifyScanEventSource = eventSource;

            eventSource.addEventListener('start', function(e) {
                const data = JSON.parse(e.data);
                console.log('Scan started. Total songs:', data.totalSongs);
            });

            eventSource.addEventListener('progress', function(e) {
                const data = JSON.parse(e.data);
                scanButton.html(`<i class="fas fa-spinner fa-spin"></i> Scanning... ${data.progress}%`);
            });

            eventSource.addEventListener('complete', function(e) {
                eventSource.close();

                scanButton.html('<i class="fab fa-spotify"></i> Scan');
                scanButton.removeClass('scanning');

                showNotification('Spotify scan completed successfully', 'success');

                fetchAlbumDetails(albumId, function(error, album) {
                    if (!error && album) {
                        renderAlbumSongs(album);
                    }
                });
            });

            eventSource.addEventListener('error', function(e) {
                let errorMessage = 'Error during scan';

                try {
                    if (e.data) {
                        const data = JSON.parse(e.data);
                        errorMessage = data.message || errorMessage;
                    }
                } catch (err) {}

                showNotification(errorMessage, 'error');

                eventSource.close();

                scanButton.html('<i class="fab fa-spotify"></i> Scan');
                scanButton.removeClass('scanning');
            });

            eventSource.onerror = function() {
                eventSource.close();

                showNotification('Connection error during scan', 'error');

                scanButton.html('<i class="fab fa-spotify"></i> Scan');
                scanButton.removeClass('scanning');
            };
        }

        function updateArtistName(albumId, newArtistName) {
            return $.ajax({
                url: '/updateAlbumArtistName',
                type: 'GET',
                data: {
                    album_id: albumId,
                    artist_name: newArtistName
                },
                dataType: 'json'
            });
        }

        function updateArtistNameUI(albumId, newArtistName) {
            // Update the displayed artist name
            $('#artist-name-display').text(newArtistName);

            // Update the edit button's data attribute
            $(`.edit-artist-name-btn[data-album-id="${albumId}"]`).data('artist', newArtistName);

            // Also update the album object in memory if it exists
            const albumIndex = albums.findIndex(a => a.id === parseInt(albumId));
            if (albumIndex !== -1) {
                albums[albumIndex].artist = newArtistName;
            }
        }

        function setupArtistNameEdit() {
            $(document).on('click', '.edit-artist-name-btn', function(e) {
                e.preventDefault();
                const albumId = $(this).data('album-id');
                const currentArtist = $(this).data('artist');
                const artistId = $(this).data('artist-id') || '';
                $('#dialog-task-title').text('Edit Artist');
                $('#artist_name').val(currentArtist);
                $('#edit_album_id').val(albumId);
                $('#artist_id').val(artistId);
                $('#artist-validation-feedback').html('');
                $('#dialog_add_artist').modal('show');
            });

            $(document).on('click', '#check-artist-name-btn', function() {
                checkArtistName();
            });
        }

        function checkArtistName() {
            const artistName = $('#artist_name').val();
            const feedbackElement = $('#artist-validation-feedback');

            // Show loading state
            feedbackElement.html('<i class="fas fa-spinner fa-spin mr-1"></i> Checking...');

            // Call the validation API
            $.ajax({
                url: '/album/check/artist',
                type: 'GET',
                data: {
                    artist_name: artistName,
                    edit_album_id: $('#edit_album_id').val()
                },
                dataType: 'json',
                success: function(result) {
                    if (result.status === 'valid') {
                        feedbackElement.html('<i class="fas fa-check-circle text-success mr-1"></i> ' + result
                            .message);
                    } else {
                        feedbackElement.html('<i class="fas fa-times-circle text-danger mr-1"></i> ' + result
                            .message);
                    }
                },
                error: function(error) {
                    console.error('Error checking artist name:', error);
                    feedbackElement.html(
                        '<i class="fas fa-exclamation-triangle text-warning mr-1"></i> Error during validation'
                        );
                }
            });
        }

        function distribute(id) {
            $("#album-details-distribute-btn").html(`<i class="fas fa-spinner fa-spin"></i> Loading...`);
            $(`#btn-quick-dis-${id}`).html(`<i class="fas fa-spinner fa-spin"></i>`);
            $.ajax({
                type: "GET",
                url: "/sendAlbumToSalad",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    logger('distribute', data);
                    $("#album-details-distribute-btn").html(
                        `<i class="fas fa-solid fa-rocket"></i> Distribute`);
                    $(`#btn-quick-dis-${id}`).html(`<i class="fas fa-solid fa-rocket"></i>`);
                    showNotification(data.message, data.status);
                    if (data.status == "success") {
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    $("#album-details-distribute-btn").html(
                    `<i class="fas fa-solid fa-rocket"></i> Distribute`);
                    $(`#btn-quick-dis-${id}`).html(`<i class="fas fa-solid fa-rocket"></i>`);
                    showNotification("Error distributing album. Please try again.", "error");
                }
            });
        }

        function applyFilters() {
            const searchTerm = $('#album-search').val().toLowerCase().trim();
            const selectedStatus = $('.filter-btn.active').data('status');
            const currentView = getViewPreference();
            let visibleCount = 0;
            if (currentView === 'table') {
                // Xử lý cho table view
                $('.albums-table tbody tr').each(function() {
                    const row = $(this);

                    // Lấy dữ liệu từ các cột (chú ý index của cột)
                    const albumId = row.find('.album-id-badge').text().toLowerCase();
                    const albumTitle = row.find('td').eq(1).text().toLowerCase(); // Cột 2 - Title
                    const albumArtist = row.find('td').eq(2).text().toLowerCase(); // Cột 3 - Artist
                    const albumGenre = row.find('td').eq(3).text().toLowerCase(); // Cột 4 - Genre
                    const albumDate = row.find('td').eq(4).text().toLowerCase(); // Cột 5 - Release Date
                    const albumSongs = row.find('td').eq(5).text().toLowerCase(); // Cột 6 - Songs
                    const albumCreator = row.find('td').eq(6).text().toLowerCase(); // Cột 7 - Creator
                    const albumStatusText = row.find('.badge').text().trim(); // Status badge text

                    // Check search match
                    const matchesSearch = searchTerm === '' ||
                        albumId.includes(searchTerm) ||
                        albumTitle.includes(searchTerm) ||
                        albumArtist.includes(searchTerm) ||
                        albumGenre.includes(searchTerm) ||
                        albumDate.includes(searchTerm) ||
                        albumSongs.includes(searchTerm) ||
                        albumCreator.includes(searchTerm) ||
                        albumStatusText.toLowerCase().includes(searchTerm);

                    // Check status match
                    let matchesStatus = true;
                    if (selectedStatus !== 'all') {
                        switch (selectedStatus) {
                            case 'not-distributed':
                                matchesStatus = albumStatusText === 'Not Distributed';
                                break;
                            case 'pending':
                                matchesStatus = albumStatusText === 'Pending Distribution' || albumStatusText ===
                                    'Pending';
                                break;
                            case 'distributing':
                                matchesStatus = albumStatusText === 'Distributing';
                                break;
                            case 'distributed':
                                matchesStatus = albumStatusText === 'Distributed';
                                break;
                            case 'error':
                                matchesStatus = albumStatusText === 'Distribution Error' || albumStatusText ===
                                    'Error';
                                break;
                            case 'online':
                                matchesStatus = albumStatusText === 'Online';
                                break;
                        }
                    }

                    // Show/hide row
                    if (matchesSearch && matchesStatus) {
                        row.show();
                        visibleCount++;
                    } else {
                        row.hide();
                    }
                });
            } else {
                $('.card.album-card').each(function() {
                    const cardElement = $(this);
                    const parentElement = cardElement.closest('.col-md-3');

                    // Get album data
                    const albumId = cardElement.find('.album-meta-item').eq(0).text().toLowerCase();
                    const albumTitle = cardElement.find('.album-title').text().toLowerCase();
                    const albumArtist = cardElement.find('.album-meta-item').eq(0).text().toLowerCase();
                    const albumDate = cardElement.find('.album-meta-item').eq(1).text().toLowerCase();
                    const albumSongs = cardElement.find('.album-meta-item').eq(2).text().toLowerCase();
                    const albumGenre = cardElement.find('.album-genre').text().toLowerCase();
                    const albumStatusText = cardElement.find('.album-status-badge').text().trim();
                    const albumUsername = cardElement.find('.user-avatar').attr('title').toLowerCase();

                    // Check search match
                    const matchesSearch = searchTerm === '' ||
                        albumTitle.includes(searchTerm) ||
                        albumId.includes(searchTerm) ||
                        albumArtist.includes(searchTerm) ||
                        albumDate.includes(searchTerm) ||
                        albumSongs.includes(searchTerm) ||
                        albumUsername.includes(searchTerm) ||
                        albumGenre.includes(searchTerm) ||
                        albumStatusText.toLowerCase().includes(searchTerm);

                    // Check status match
                    let matchesStatus = true;
                    if (selectedStatus !== 'all') {
                        switch (selectedStatus) {
                            case 'not-distributed':
                                matchesStatus = albumStatusText === 'Not Distributed';
                                break;
                            case 'pending':
                                matchesStatus = albumStatusText === 'Pending Distribution';
                                break;
                            case 'distributing':
                                matchesStatus = albumStatusText === 'Distributing';
                                break;
                            case 'distributed':
                                matchesStatus = albumStatusText === 'Distributed';
                                break;
                            case 'error':
                                matchesStatus = albumStatusText === 'Error';
                                break;
                            case 'online':
                                matchesStatus = albumStatusText === 'Online';
                                break;
                        }
                    }

                    // Show/hide based on both filters
                    if (matchesSearch && matchesStatus) {
                        parentElement.show();
                        visibleCount++;
                    } else {
                        parentElement.hide();
                    }
                });
            }
            if (visibleCount === 0) {
                if ($('#no-results-message').length === 0) {
                    $('#albums-list').append(`
                <div id="no-results-message" class="col-12 text-center my-5 py-5">
                    <i class="fas fa-search text-muted fa-2x mb-3"></i>
                    <p class="text-muted w-100">No albums match your filters.</p>
                    <button class="btn btn-outline-primary btn-sm mt-2 reset-filters-btn">
                        <i class="fas fa-redo mr-1"></i> Reset Filters
                    </button>
                </div>
            `);

                    // Add reset filters button handler
                    $('.reset-filters-btn').click(function() {
                        $('#album-search').val('');
                        $('.filter-btn').removeClass('active');
                        $('.filter-btn[data-status="all"]').addClass('active');
                        applyFilters();
                    });
                }
            } else {
                $('#no-results-message').remove();
            }
            setTimeout(() => {
                setupLazyLoading();
            }, 50);
        }

        function saveViewPreference(view) {
            localStorage.setItem('albumViewType', view);
        }

        function getViewPreference() {
            return localStorage.getItem('albumViewType') || 'grid';
        }

        function updateViewSwitcher(view) {
            $('.view-btn').removeClass('active');
            $(`.view-btn[data-view="${view}"]`).addClass('active');
        }

        function renderAlbumsTable() {
            const albumsContainer = $('#albums-list');
            albumsContainer.empty();

            if (!albums.length) {
                // Empty state (giữ nguyên)
                const emptyStateHTML = `...`; // giữ nguyên code cũ
                albumsContainer.html(emptyStateHTML);
                return;
            }

            // Create table HTML với thiết kế mới
            let tableHTML = `
    <div class="col-12">
        <div class="table-view">
            <table class="albums-table">
                <thead>
                    <tr>
                        <th class="album-id-cell">ID</th>
                        <th class="album-cover-cell"></th>
                        <th>Album</th>
                        <th>Genre</th>
                        <th>Release Date</th>
                        <th>Songs</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th class="actions-cell"></th>
                    </tr>
                </thead>
                <tbody>
    `;

            albums.forEach(album => {
                const songCount = album.songs.length;

                // Status HTML (giữ nguyên logic)
                let statusHTML = '';
                switch (album.distributed) {
                    case 0:
                        statusHTML = '<span class="badge badge-secondary">Not Distributed</span>';
                        break;
                    case 1:
                        statusHTML = '<span class="badge badge-warning">Pending Distribution</span>';
                        break;
                    case 2:
                        statusHTML = '<span class="badge badge-info">Distributing</span>';
                        break;
                    case 3:
                        statusHTML = '<span class="badge badge-success">Distributed</span>';
                        break;
                    case 4:
                        statusHTML = '<span class="badge badge-danger">Error</span>';
                        break;
                    case 5:
                        statusHTML = '<span class="badge badge-primary">Online</span>';
                        break;
                    default:
                        statusHTML = '<span class="badge badge-secondary">Unknown</span>';
                }

                // Format release date
                const releaseDate = new Date(album.releaseDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                // Actions button
                const distributeButton = album.distributed === 0 ?
                    `<button class="distribute-btn-small distribute-btn" data-album-id="${album.id}" title="Distribute">
                    <i class="fas fa-rocket"></i>
                </button>` :
                    '';
                const chartButton = album.distributed === 5 ? `
                  <button class="btn-artist-stats distribute-btn-small" title="View Artist Stats" data-artist="${encodeURIComponent(album.artist)}" data-album-id="${album.id}">
                    <i class="fas fa-chart-line"></i>
                  </button>
                ` : '';
                const artistAlbumCount = getArtistAlbumCount(album.artist);
                const artistAlbumCountText = artistAlbumCount > 1 ?
                    `<span class="artist-album-count-table" data-artist="${album.artist}" title="Click to search this artist">${artistAlbumCount} albums</span>` :
                    '';
                const artistTotalStreamsText = album.artist_total_streams !== null ?
                    `<span class="artist-total-streams ml-2" title="Total stream, last update ${album.last_update_ago || 'unknown'}">
                    <i class="fas fa-headphones"></i> ${album.artist_total_streams.toLocaleString()}
                            </span>` : '';
                const cidColor  = album.youtube_claim == 5?"#28a745":"#ebdb0e";        
                const coverImgHtml = album.coverImg ? `
                        <div class="table-album-cover-wrapper" style="position: relative; width: 56px; height: 56px;">
                            <div class="table-cover-placeholder" style="
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 56px;
                                height: 56px;
                                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                                border-radius: 8px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #6c757d;
                                font-size: 16px;
                                z-index: 1;
                                transition: opacity 0.3s ease;
                            ">
                                <i class="fas fa-music"></i>
                            </div>

                            <div class="table-image-loading-spinner" style="
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                z-index: 3;
                                display: none;
                            ">
                                <i class="fas fa-spinner fa-spin text-primary" style="font-size: 14px;"></i>
                            </div>

                            <img data-src="${album.coverImg}" 
                                 src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 56 56'%3E%3Crect width='56' height='56' fill='%23f8f9fa'/%3E%3C/svg%3E"
                                 class="album-cover-small album-cover lazy-loading" 
                                 alt="${album.name}"
                                 style="
                                    position: relative;
                                    z-index: 2;
                                    width: 56px;
                                    height: 56px;
                                    object-fit: cover;
                                    border-radius: 8px;
                                    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
                                    transition: all 0.3s ease;
                                    opacity: 0;
                                 ">
                        </div>
                    ` : `
                        <div class="table-cover-placeholder" style="
                            width: 56px;
                            height: 56px;
                            background: #f8d7da;
                            border-radius: 8px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: #721c24;
                            font-size: 16px;
                        ">
                            <i class="fas fa-music"></i>
                        </div>
                    `;

                tableHTML += `
            <tr class="album-row" data-album-id="${album.id}">
                   <td class="album-id-cell">
        <span class="album-id-badge">#${album.id}</span>
    </td>
                <td class="album-cover-cell">
                    ${coverImgHtml}
                </td>
                <td>
                    <div class="album-title-cell">${album.name}</div>
                    <div class="album-subtitle">
                        ${album.artist}
                        ${album.artist_youtube_claim == 1 ? '<i class="fas fa-check-circle ml-1" style="color: #17a2b8;" data-toggle="tooltip" title="CID enabled for this artist"></i>' : ''}
                        ${artistAlbumCountText} 
                        ${artistTotalStreamsText}
                        ${album.youtube_claim == 1 ? '<i class="fas fa-dollar-sign ml-2" style="color: '+cidColor+';" data-toggle="tooltip" title="CID enabled for this album"></i>' : ''}
                    </div>
                </td>
                <td>
                    <span class="genre-pill">${album.genre}</span>
                </td>
                <td>
                    <span class="release-date">${releaseDate}</span>
                </td>
                <td>
                    <span class="songs-count artist-total-streams">
                        <i class="fas fa-music"></i> ${songCount}
                    </span>
                </td>
                <td>
                    <div class="creator-cell">
                        <img src="/images/avatar/${album.username}.jpg" class="creator-avatar-small" alt="${album.username}">
                        <span>${album.username}</span>
                    </div>
                </td>
                <td>${statusHTML}</td>
                <td class="actions-cell">${distributeButton}  ${chartButton}

                </td>
            </tr>
        `;
            });

            tableHTML += `
                </tbody>
            </table>
        </div>
    </div>
    `;

            albumsContainer.html(tableHTML);
            // Event handlers
            $('.album-row').click(function(e) {
                // Prevent opening album detail if clicking on artist stats button
                if (
                    $(e.target).closest('.btn-artist-stats').length > 0 ||
                    $(e.target).hasClass('btn-artist-stats') ||
                    $(e.target).closest('.artist-album-count-table').length > 0 ||
                    $(e.target).hasClass('artist-album-count-table') ||
                    $(e.target).closest('.artist-total-streams').length > 0 ||
                    $(e.target).hasClass('artist-total-streams')
                ) {
                    return;
                }
                if (!$(e.target).hasClass('distribute-btn') && !$(e.target).parent().hasClass('distribute-btn')) {
                    const albumId = $(this).data('album-id');
                    showAlbumDetails(albumId);
                }
            });

            $('.distribute-btn').click(function(e) {
                e.stopPropagation();
                const albumId = $(this).data('album-id');
                distribute(albumId);
            });

            // Artist stats button: open modal only
            $(document).off('click', '.btn-artist-stats').on('click', '.btn-artist-stats', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const artist = decodeURIComponent($(this).data('artist'));
                // Open modal and set iframe src
                $('#artistStatsModal').modal('show');
                $('#artist-stats-iframe').attr('src',
                    `https://distro.360promo.fm/iframe/charts/${encodeURIComponent(artist)}`);
            });
        }

        function renderAlbumsWithView() {
            const currentView = getViewPreference();

            if (currentView === 'table') {
                renderAlbumsTable();
            } else {
                renderAlbums(); // Original grid view function
            }

            updateViewSwitcher(currentView);

            updateFilterCounts();

            // Apply filters after rendering
            setTimeout(() => {
                setupLazyLoading();
                applyFilters();
                setupArtistCountClick();
            }, 100);
        }

        function getArtistAlbumCount(artistName) {
            if (!albums || !albums.length) return 0;

            // Đếm số album có cùng tên ca sĩ
            return albums.filter(album => album.artist === artistName).length;
        }

        let currentModalAudio = null;

        let currentUploadedImageUrl = null;

        function validateImageFile(file) {
            const errors = [];

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                errors.push('Please select a valid image file (JPEG, PNG, GIF, WebP)');
            }

            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                errors.push('Image size must not exceed 5MB');
            }

            return errors;
        }

        function validateImageDimensions(file) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = function() {
                    const width = this.width;
                    const height = this.height;

                    if (width < 1400 || height < 1400) {
                        reject(['Image dimensions must be at least 1400x1400 pixels. Current: ' + width + 'x' +
                            height
                        ]);
                        return;
                    }

                    resolve({
                        width,
                        height
                    });
                };

                img.onerror = function() {
                    reject(['Invalid image file']);
                };

                img.src = URL.createObjectURL(file);
            });
        }

        function showImageUploadLoading() {
            $('#imagePreview').html(`
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                <span class="text-muted">Uploading...</span>
            </div>
        `);
        }

        function showImageUploadError(message) {
            $('#imagePreview').html(`
            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <small class="text-center">${message}</small>
            </div>
        `);
        }

        async function uploadImageToCDN(file) {
            try {
                showImageUploadLoading();
                const validationErrors = validateImageFile(file);
                if (validationErrors.length > 0) {
                    showImageUploadError(validationErrors.join('<br>'));
                    return false;
                }
                try {
                    await validateImageDimensions(file);
                } catch (dimensionErrors) {
                    showImageUploadError(dimensionErrors.join('<br>'));
                    return false;
                }

                const uploadLinkResponse = await fetch('/album/upload-image-link', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    body: JSON.stringify({
                        filename: file.name,
                        filesize: Math.round(file.size / 1024), // Convert to KB
                        mimetype: file.type
                    })
                });

                const uploadLinkData = await uploadLinkResponse.json();

                if (uploadLinkData.status !== 'success') {
                    showImageUploadError(uploadLinkData.message || 'Failed to get upload link');
                    return false;
                }

                const uploadResponse = await fetch(uploadLinkData.presigned_url, {
                    method: 'PUT',
                    body: file,
                    headers: {
                        'Content-Type': file.type
                    }
                });

                if (!uploadResponse.ok) {
                    throw new Error('Upload failed with status: ' + uploadResponse.status);
                }

                currentUploadedImageUrl = uploadLinkData.public_url;

                $('#imagePreview').html(`
                <img src="${currentUploadedImageUrl}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;" alt="Album cover preview">
                <div class="position-absolute" style="top: 5px; right: 5px;">
                    <i class="fas fa-check-circle text-success fa-lg"></i>
                </div>
            `);

                showNotification('Image uploaded successfully!', 'success');

                return true;

            } catch (error) {
                console.error('Upload error:', error);
                showImageUploadError('Upload failed. Please try again.');
                showNotification('Upload failed: ' + error.message, 'error');
                return false;
            }
        }

        function handleImageInputChange(event) {
            const file = event.target.files[0];

            if (!file) {
                $('#imagePreview').html(`<i class="fas fa-image fa-3x music-icon"></i>`);
                currentUploadedImageUrl = null;
                return;
            }

            uploadImageToCDN(file);
        }

        function setupArtistCountClick() {
            // Xóa event cũ để tránh trùng lặp
            $(document).off('click', '.artist-album-count-table');

            // Thêm event handler cho click vào artist count
            $(document).on('click', '.artist-album-count-table', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                const artistName = $(this).data('artist');
                if (artistName) {
                    // Nhập tên nghệ sỹ vào ô search
                    $('#album-search').val(artistName);

                    // Trigger search
                    $('#album-search').trigger('input');

                    // Optional: Focus vào ô search và highlight text
                    $('#album-search').focus().select();

                    // Optional: Thêm visual feedback
                    $(this).addClass('clicked');
                    setTimeout(() => {
                        $(this).removeClass('clicked');
                    }, 200);
                }
            });
        }

        function loadArtistList(page = 1) {
            $('#artist-loading').show();
            $('#artist-table-body').empty();

            const filters = {
                page: page,
                per_page: artistPerPage,
                artist_name: $('#artist-name-filter').val().trim(),
                youtube_claim: $('#youtube-claim-filter').val()
            };

            // Parse sort options
            const sortOption = $('#artist-sort').val().split('|');
            if (sortOption.length === 2) {
                filters.sort = sortOption[0];
                filters.direction = sortOption[1];
            }

            $.ajax({
                url: '/artist/list',
                method: 'GET',
                data: filters,
                success: function(response) {
                    $('#artist-loading').hide();

                    if (response.status === 'success') {
                        renderArtistTable(response.data.data);
                        renderArtistPagination(response.data);
                        updateArtistPaginationInfo(response.data);
                    } else {
                        showNotification('Error loading artist list: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    $('#artist-loading').hide();
                    console.error('Error loading artist list:', error);
                    showNotification('Error loading artist list. Please try again.', 'error');
                }
            });
        }

        // Render Artist Table
        let currentArtistPage = 1;
        let artistPerPage = 20;
        function renderArtistTable(artists) {
            const tbody = $('#artist-table-body');
            tbody.empty();

            if (artists.length === 0) {
                tbody.append(`
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <br>No artists found
                </td>
            </tr>
        `);
                return;
            }

            artists.forEach(artist => {
                const totalStreams = artist.artist_total_streams ?
                    `<span class="artist-streams"><i class="fas fa-headphones"></i>${parseInt(artist.artist_total_streams).toLocaleString()}</span>` :
                    '<span class="text-muted">-</span>';

                const createdDate = artist.created ?
                    new Date(artist.created).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) : '-';

                const row = `
            <tr>
                <td><span class="artist-id-badge">#${artist.id}</span></td>
                <td>${artist.username || '-'}</td>
                <td><strong>${artist.artist_name}</strong></td>
                <td>${totalStreams}</td>
                <td>
                    <label class="cherry-switch">
                        <input type="checkbox" ${artist.youtube_claim == 1 ? 'checked' : ''} 
                               onchange="toggleYoutubeClaim(${artist.id}, this.checked)">
                        <span class="cherry-slider"></span>
                    </label>
                </td>
                <td>${createdDate}</td>
            </tr>
        `;
                tbody.append(row);
            });
        }

        // Toggle YouTube Claim Status
        function toggleYoutubeClaim(artistId, isEnabled) {
            const status = isEnabled ? 1 : 0;
             const csrfToken = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/artist/update-youtube-claim',
                method: 'POST',
                data: {
                    artist_id: artistId,
                    youtube_claim: status,
                    _token: csrfToken
                },
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification(response.message, 'success');
                    } else {
                        showNotification('Error: ' + response.message, 'error');
                        // Revert switch if failed
                        $(`input[onchange="toggleYoutubeClaim(${artistId}, this.checked)"]`).prop('checked', !
                            isEnabled);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating YouTube claim:', error);
                    showNotification('Error updating YouTube claim status. Please try again.', 'error');
                    // Revert switch if failed
                    $(`input[onchange="toggleYoutubeClaim(${artistId}, this.checked)"]`).prop('checked', !
                        isEnabled);
                }
            });
        }

        // Render Artist Pagination
        function renderArtistPagination(data) {
            const pagination = $('#artist-pagination');
            pagination.empty();

            if (data.last_page <= 1) {
                return;
            }

            // Previous button
            if (data.current_page > 1) {
                pagination.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadArtistList(${data.current_page - 1}); return false;">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `);
            }

            // Page numbers
            const startPage = Math.max(1, data.current_page - 2);
            const endPage = Math.min(data.last_page, data.current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === data.current_page ? 'active' : '';
                pagination.append(`
            <li class="page-item ${activeClass}">
                <a class="page-link" href="#" onclick="loadArtistList(${i}); return false;">${i}</a>
            </li>
        `);
            }

            // Next button
            if (data.current_page < data.last_page) {
                pagination.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadArtistList(${data.current_page + 1}); return false;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `);
            }
        }

        // Update Pagination Info
        function updateArtistPaginationInfo(data) {
            const info = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} results`;
            $('#artist-pagination-info').text(info);
        }

        $(document).ready(function() {
            window.albums = [];
            window.currentAlbumId = null;
            window.currentAlbumData = null;
            window.selectedSongs = [];
            window.currentAudio = null;
            window.currentModalAudio = null;


            fetchAlbums();

            setupAddSongsModal();

            setupProgressBarDrag();

            $('#addSongsModal').on('show.bs.modal', function() {
                renderAvailableSongs();
            });

            $('#addSongsModal').on('hidden.bs.modal', function() {
                selectedSongs = [];
                updateSelectedCount();
            });

            $('#add-songs-btn').on('click', function() {
                if (validateSelectedSongs()) {
                    handleAddMultipleSongsToAlbum();
                }
            });

            setupAlbumSearch();

            $('#show-all-albums').click(function(e) {
                e.preventDefault();
                $('#album-details').hide();
                $('#albums-container').show();
                currentAlbumId = null;

                // Dừng phát nhạc nếu đang phát
                const audioElement = $('#audio-element')[0];
                if (audioElement) {
                    audioElement.pause();
                    $('#audio-player').addClass('d-none');
                }
            });

            $('#player-play-btn').click(function() {
                const audioElement = $('#audio-element')[0];

                if (audioElement.paused) {
                    audioElement.play();
                    $(this).html('<i class="fas fa-pause"></i>').addClass('playing');
                    if (currentAudio) {
                        $(`.play-song-btn[data-song-id="${currentAudio.id}"]`).html(
                            '<i class="fas fa-pause"></i>').addClass('playing');
                    }
                } else {
                    audioElement.pause();

                    $(this).html('<i class="fas fa-play"></i>').removeClass('playing');
                    if (currentAudio) {
                        $(`.play-song-btn[data-song-id="${currentAudio.id}"]`).html(
                            '<i class="fas fa-play"></i>').removeClass('playing');
                    }
                }
            });


            $('#audio-element').off('timeupdate').on('timeupdate', updateMainAudioProgress);

            $('#audio-element').on('ended', function() {
                $('#player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                if (currentAudio) {
                    $(`.play-song-btn[data-song-id="${currentAudio.id}"]`).html(
                        '<i class="fas fa-play"></i>').removeClass('playing');
                }
                currentAudio = null;
            });

            addReleaseDateEditStyles();

            setupReleaseDateEdit();

            setupArtistNameEdit();

            $(document).on('click', '.album-spotify-link', function() {
                const spotifyUrl = $(this).data('spotify-url');

                copyToClipboard2(spotifyUrl);

                $(this).addClass('copied');
                setTimeout(() => {
                    $(this).removeClass('copied');
                }, 2000);

                const isArtist = $(this).find('i').hasClass('fa-user');
                showNotification(`Spotify ${isArtist ? 'artist' : 'album'} link copied to clipboard`,
                    "success");
            });

            $('.filter-btn').click(function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                applyFilters();
            });

            $('.view-btn').click(function() {
                const view = $(this).data('view');
                saveViewPreference(view);
                renderAlbumsWithView();
            });

            const currentView = getViewPreference();
            updateViewSwitcher(currentView);

            $('#album-search').on('input', function() {
                applyFilters();
            });

            if ($('.filter-btn').length > 0 && albums.length > 0) {
                updateFilterCounts();
            }

            $('#albumCover').off('change').on('change', handleImageInputChange);

            $('#submitAlbum').off('click').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.html(`<i class="fas fa-spinner fa-spin"></i> Loading...`);

                const albumTitle = $('#albumTitle').val();
                const albumArtist = $('#albumArtist').val();
                const albumGenre = $('#albumGenre').val();
                const instruments = $('#instruments').val();
                const albumGenreText = $('#albumGenre option:selected').text();
                const editMode = $("#edit_mode").val();

                // Check required fields
                if (!albumTitle || !albumArtist || !albumGenre) {
                    showNotification("Please fill in all required album information!", "error");
                    $this.html(`<i class="fas fa-plus-circle mr-1"></i> Save`);
                    return;
                }

                // Check if image is required (new album) and uploaded
                if (editMode == 0 && !currentUploadedImageUrl) {
                    showNotification("Please upload an album cover image!", "error");
                    $this.html(`<i class="fas fa-plus-circle mr-1"></i> Save`);
                    return;
                }

                $('#submitBtn').attr('disabled', true);

                const formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('title', albumTitle);
                formData.append('artist', albumArtist);
                formData.append('genre', albumGenre);
                formData.append('genreText', albumGenreText);
                formData.append('releaseDate', $('#releaseDate').val());
                formData.append('instruments', instruments);
                formData.append('album_id', $('#edit_album_id').val());
                formData.append('edit_mode', editMode);

                // Add uploaded image URL instead of file
                if (currentUploadedImageUrl) {
                    formData.append('uploaded_image_url', currentUploadedImageUrl);
                }

                fetch('/addAlbum', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error! status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status == "success") {
                            showNotification("Album saved successfully!", "success");
                            $("#form-album")[0].reset();
                            $('#imagePreview').html(`<i class="fas fa-image fa-3x music-icon"></i>`);
                            currentUploadedImageUrl = null;
                            $("#addAlbumModal").modal("hide");
                            location.reload();
                        } else {
                            showNotification(data.message, "error");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error: ' + error.message, "error");
                    })
                    .finally(() => {
                        $this.html(`<i class="fas fa-plus-circle mr-1"></i> Save`);
                        $('#submitBtn').attr('disabled', false);
                    });
            });

            $(".btn-create-album").off('click').on('click', function() {
                // Reset form and image state
                $("#form-album")[0].reset();
                $('#imagePreview').html(`<i class="fas fa-image fa-3x music-icon"></i>`);
                currentUploadedImageUrl = null;

                // Set default release date
                $('#releaseDate').val(new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0]);

                // Reset other controls
                $('#instruments').selectpicker('refresh');
                $('#albumArtist').selectpicker('refresh');
                $('#edit_mode').val('0');
                $('#edit_album_id').val('');

                $("#addAlbumModal").modal("show");
                artistList();
            });

            $('#imagePreview').click(function() {
                $('#albumCover').click();
            });
            $('#artist-management-btn').click(function() {
                $('#artistManagementModal').modal('show');
                loadArtistList();
            });

            // Filter button click
            $('#artist-filter-btn').click(function() {
                currentArtistPage = 1;
                loadArtistList();
            });

            // Sort change
            $('#artist-sort').change(function() {
                currentArtistPage = 1;
                loadArtistList();
            });

            // Enter key on filter input
            $('#artist-name-filter').keypress(function(e) {
                if (e.which === 13) {
                    $('#artist-filter-btn').click();
                }
            });
        });
    </script>
@endsection
