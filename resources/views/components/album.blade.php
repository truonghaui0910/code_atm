@extends('layouts.master')

@section('content')
    <style>
        body {
            background-color: #f8f9fa;
            font-size: 16px;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .album-card .card-img-top {
            height: 150px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .song-item.selected {
            background-color: #e3f2fd;
            border-left: 3px solid #0056b3;
        }

        .album-badge {
            font-size: 0.7em;
            padding: 3px 8px;
        }

        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
        }

        .search-container {
            position: relative;
        }

        .search-container .form-control {
            padding-left: 35px;
            border-radius: 20px;
        }

        .action-btn {
            border-radius: 20px;
        }

        .album-details-container {
            display: none;
        }

        .btn-create-album {
            background-color: #6200ea;
            color: white;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(98, 0, 234, 0.2);
            transition: all 0.3s;
        }

        .btn-create-album:hover {
            background-color: #5000ca;
            box-shadow: 0 6px 12px rgba(98, 0, 234, 0.3);
            color: white;
        }

        .btn-distribute {
            background-color: #00c853;
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            padding: 5px 15px;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 200, 83, 0.3);
        }

        .btn-distribute:hover {
            background-color: #00a844;
            color: white;
        }

        .btn-play {
            color: #3d5afe;
            background-color: #e8eaf6;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            line-height: 32px;
            text-align: center;
            padding: 0;
            transition: all 0.2s;
        }

        .btn-play:hover {
            background-color: #3d5afe;
            color: white;
        }

        .distribute-pill {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        .player-song-info {
            margin-left: 15px;
            flex-grow: 1;
        }

        .album-footer {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .album-footer .song-count {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .album-action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-distribute-album {
            width: 100%;
            border-radius: 20px;
            font-size: 0.85rem;
            padding: 6px 0;
            background-color: #00c853;
            color: white;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 200, 83, 0.2);
            transition: all 0.2s;
        }

        .btn-distribute-album:hover {
            background-color: #00a844;
            box-shadow: 0 4px 8px rgba(0, 200, 83, 0.3);
            color: white;
        }

        .album-detail-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .album-detail-title {
            display: flex;
            align-items: center;
        }

        .album-detail-title h5 {
            margin-bottom: 0;
            margin-left: 15px;
            font-weight: 600;
        }

        .btn-back {
            min-width: 36px;
            height: 36px;
            border-radius: 18px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            color: #6c757d;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .btn-back:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        .album-action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            height: 36px;
            border-radius: 18px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-action i {
            margin-right: 8px;
        }

        .btn-action.btn-add-songs {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-action.btn-add-songs:hover {
            background-color: #0069d9;
        }

        .btn-action.btn-distribute-detail {
            background-color: #00c853;
            color: white;
            border: none;
        }

        .btn-action.btn-distribute-detail:hover {
            background-color: #00a844;
        }

        .song-item:hover {
            background-color: #f8f9fa;
        }

        .song-title {
            font-weight: 600;
            margin-bottom: 2px;
            color: #212529;
        }

        .song-artist {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .song-duration {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .audio-player {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            background-color: #f8f9fe;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        .player-controls {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .btn-play-song {
            font-size: .875rem;
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #2D0A31;
            color: white;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-play-song:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            border: none;
        }

        .song-item {
            margin-bottom: 2px;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .song-item .d-flex {
            align-items: center;
        }

        .song-info {
            margin-left: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .album-details-card {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .album-cover-container {
            position: relative;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .album-cover-img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .album-cover-container:hover .album-cover-img {
            transform: scale(1.03);
        }

        .album-meta-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .album-meta-item:last-child {
            border-bottom: none;
        }

        .album-meta-label {
            color: #6c757d;
            font-weight: 500;
        }

        .album-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .album-status.distributed {
            background-color: #d4edda;
            color: #28a745;
        }

        .album-status.not-distributed {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .album-songs-count {
            display: inline-flex;
            align-items: center;
        }

        .album-songs-count i {
            margin-right: 5px;
            color: #007bff;
        }

        .container {
            margin-left: 70px;
            width: 90%;
            max-width: none;
            padding-right: 15px;
        }

        .album-card .card-body {
            padding: 15px;
        }

        .album-meta span {
            display: inline-block;
            margin-right: 12px;
        }

        .album-meta i {
            margin-right: 4px;
        }

        .album-meta-value {
            font-weight: 600;
            color: #343a40;
            text-align: right;
        }

        .genre-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .genre-pill {
            background-color: #e9ecef;
            color: #495057;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }

        #album-search:focus+.search-icon+.search-clear,
        #album-search:not(:placeholder-shown)+.search-icon+.search-clear {
            display: block;
        }

        .album-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .album-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .album-cover-wrapper {
            position: relative;
        }

        .album-status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 20px;
        }

        .distribute-button {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: #00c853;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
            z-index: 10;
        }

        .distribute-button:hover {
            background-color: #00a844;
            transform: scale(1.1);
        }

        .distribute-button i {
            font-size: 18px;
        }

        .card-body {
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .album-title {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 12px;
            color: #343a40;
        }

        .album-meta {
            /*margin-bottom: 15px;*/
        }

        .album-meta-item {
            display: flex;
            align-items: center;
            color: #495057;
            font-size: 15px;
            /*font-weight: 500;*/
            margin-bottom: 6px;
        }

        .album-meta-item i {
            width: 20px;
            margin-right: 6px;
            color: #6c757d;
        }

        .album-genre {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            /*margin-bottom: 12px;*/
            width: auto;
            max-width: fit-content;
        }

        .album-status-badge.distributed {
            background-color: #d4edda;
            color: #28a745;
        }

        .album-status-badge.not-distributed {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .album-status-badge i {
            margin-right: 5px;
        }

        .album-info-panel {
            /*background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);*/
            border-radius: 12px;
            /*box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);*/
            padding: 10px 25px 25px 25px;
            margin-bottom: 20px;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .album-info-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6c757d;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .album-description {
            font-size: 15px;
            line-height: 1.6;
            color: #495057;
            margin-bottom: 25px;
            font-style: italic;
            padding-left: 10px;
            border-left: 3px solid #007bff;
        }

        .album-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .album-info-item {
            display: flex;
            flex-direction: column;
        }

        .album-info-label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .album-info-value {
            font-size: 16px;
            font-weight: 500;
            color: #343a40;
            display: flex;
            align-items: center;
        }

        .album-genre-badge {
            display: inline-flex;
            align-items: center;
            background: #e9ecef;
            color: #343a40;
            border-radius: 20px;
            padding: 5px 12px;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-top: 2px;
        }

        .detail-status-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 20px;
            padding: 5px 12px;
            font-weight: 500;
            font-size: 14px;
            margin-top: 2px;
        }

        .detail-status-badge.distributed {
            background-color: #d4edda;
            color: #28a745;
        }

        .detail-status-badge.not-distributed {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .album-info-value i {
            margin-right: 8px;
            width: 18px;
            color: #007bff;
        }

        .detail-status-badge i {
            margin-right: 5px;
            color: inherit;
        }

        .album-genre-badge i {
            color: #6c757d;
            margin-right: 5px;
        }

        .empty-state-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            padding: 40px;
            text-align: center;
            /* background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); */
            border-radius: 12px;
            /* box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); */
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }

        .empty-state-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: #6c757d;
            background: rgba(108, 117, 125, 0.1);
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-state-title {
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 15px;
        }

        .empty-state-description {
            font-size: 16px;
            color: #6c757d;
            max-width: 500px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-create-first-album {
            background: linear-gradient(45deg, #6200ea, #3700b3);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 30px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(98, 0, 234, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .btn-create-first-album:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(98, 0, 234, 0.4);
            background: linear-gradient(45deg, #7c4dff, #6200ea);
            color: white;
        }

        .btn-create-first-album i {
            margin-right: 10px;
            font-size: 18px;
        }

        .album-info-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .songs-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .songs-list {
            flex-grow: 1;
            overflow-y: auto;
            max-height: none;
            min-height: 400px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            background-color: #fff;
            padding: 10px;
        }

        .btn-remove-song {
            width: 32px;
            height: 32px;
            border-radius: 16px;
            background-color: #fff;
            color: #dc3545;
            border: 1px solid #dc3545;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            padding: 0;
            font-size: 16px;
        }

        .btn-remove-song:hover {
            background-color: #dc3545;
            color: white;
        }

        .modal-songs-list {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }

        .modal-song-controls {
            display: flex;
            align-items: center;
        }

        #album-songs-search-form {
            width: 60%;
        }

        /*        #album-songs-search {
                    border-radius: 0;
                }*/

        #modal-songs-search-form {
            width: 100%;
        }

        .modal-filter-container {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .genre-filter-btn {
            margin-right: 5px;
            margin-bottom: 5px;
            background-color: #e9ecef;
            color: #495057;
            border: none;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 13px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .genre-filter-btn.active {
            background-color: #007bff;
            color: white;
        }

        .genre-filter-btn:hover {
            background-color: #dee2e6;
        }

        .genre-filter-btn.active:hover {
            background-color: #0069d9;
        }

        .modal-song-item {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .modal-song-item:hover {
            background-color: #f5f5f5;
        }

        .modal-song-item.selected {
            background-color: #e3f2fd;
            border-left: 3px solid #007bff;
        }


        .no-results-message {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .btn-play.playing {
            animation: pulse 2s infinite;
        }

        .progress {
            cursor: pointer;
            height: 8px;
        }

        .progress:hover .progress-bar {
            background-color: #0056b3;
        }

        .search-form {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search-input {
            padding-left: 40px;
            padding-right: 40px;
            border-radius: 30px !important;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            height: 38px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-input:focus {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border-color: #80bdff;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }

        .search-clear {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            border: none;
            background: transparent;
            z-index: 10;
        }

        .search-clear:hover {
            color: #dc3545;
        }

        .search-submit {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            padding: 0;
            width: 20px;
            height: 20px;
            border: none;
            background: transparent;
            color: #007bff;
            cursor: pointer;
            z-index: 10;
        }

        .modern-search-container {
            position: relative;
            width: 100%;
            max-width: 350px;
            margin-bottom: 0;
        }

        .modern-search-input {
            width: 100%;
            padding: 10px 40px 10px 40px;
            border-radius: 50px !important;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
            font-size: 14px;
            background-color: #fff;
        }

        .modern-search-input:focus {
            border-color: #007bff;
            box-shadow: 0 3px 8px rgba(0, 123, 255, 0.25);
            outline: none;
        }

        .modern-search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }

        .modern-search-clear {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        .modern-search-clear:hover {
            color: #dc3545;
        }

        .progress-bar {
            background: linear-gradient(to right, #007bff, #4facfe);
            transition: width 0.1s linear;
        }

        .btn-play-song,
        .btn-play {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #2D0A31;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 4px 8px rgba(45, 10, 49, 0.2);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-play-song:hover,
        .btn-play:hover {
            /*transform: scale(1.1);*/
            background-color: #3d1042;
            box-shadow: 0 6px 12px rgba(45, 10, 49, 0.3);
        }

        .btn-play-song:active,
        .btn-play:active {
            transform: scale(0.95);
        }

        .preview-image {
            width: 100%;
            min-height: 210px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px dashed #ced4da;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            background-color: #f8f9fa;
        }
        
        .spotify-link {
            color: #1DB954;
            background-color: transparent;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: 1px solid #1DB954;
        }

        .spotify-link i {
            font-size: 18px;
        }

        .spotify-link:hover {
            background-color: #1DB954;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 2px 5px rgba(29, 185, 84, 0.3);
        }

        .spotify-link:active {
            transform: scale(0.95);
        }
        .btn-action.btn-spotify {
            background-color: #1DB954;
            color: white;
            border: none;
        }

        .btn-action.btn-spotify:hover {
            background-color: #1aa34a;
        }

        .btn-action.btn-spotify.scanning {
            background-color: #cccccc;
            pointer-events: none;
        }   
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

/*        .artist-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }*/

        .creator-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .album-creator {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

.album-spotify-links {
    margin-top: 15px;
}

.album-spotify-link {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 6px;
    background-color: #f8f9fa;
    margin-bottom: 8px;
    transition: all 0.2s;
    cursor: pointer;
    border: 1px solid #e9ecef;
}

.album-spotify-link:hover {
    background-color: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.album-spotify-link i {
    color: #1DB954;
    font-size: 18px;
    margin-right: 10px;
}

.album-spotify-link span {
    color: #495057;
    font-weight: 500;
}

.album-spotify-link.copied {
    background-color: #e3f2fd;
    border-color: #90caf9;
}

.album-spotify-link.copied span {
    color: #1976d2;
}

.album-spotify-link.copied i {
    animation: pulse 1s forwards;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}
        
    </style>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm mb-4">
            <a class="navbar-brand text-primary" href="#">
                <i class="fas fa-music mr-2"></i>Albums Manager
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#" id="show-all-albums">All Albums</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-create-album ml-3">
                            <i class="fas fa-plus mr-1"></i> Create New Album
                        </button>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="row">
            <div class="col-lg-12 mb-4" id="albums-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="fas fa-compact-disc mr-2"></i>Your Albums</h4>
                    <div class="search-container" id="album-search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Search albums..." id="album-search">

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
                            <!--                            <div>
                                    <button id="album-details-distribute-btn" class="btn btn-success mr-2 action-btn">
                                        <i class="fas fa-share-alt mr-1"></i> Distribute
                                    </button>
                                    <button class="btn btn-primary action-btn" data-toggle="modal" data-target="#addSongsModal">
                                        <i class="fas fa-plus"></i> Add Songs
                                    </button>
                                </div>-->
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
                <!--                <div class="modal-header">
                            <h5 class="modal-title" id="addSongsModalLabel">Add songs to album: <span
                                    id="modal-album-name"></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>-->
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


    @include('dialog.bom.add_album')
    @include('dialog.bom.add_artist')
@endsection

@section('script')
    <script type="text/javascript">
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
            $("#dialog_add_artist").modal("show");

        }

        function addArtist() {
            var form = $("#form-add-artist").serialize();
            $.ajax({
                type: "POST",
                url: "/albumAddArtist",
                data: form,
                dataType: 'json',
                success: function(data) {
                    logger('addArtist', data);
                    showNotification(data.message, data.status);
                    if (data.status == "success") {
                        $("#form-add-artist")[0].reset();
                        $("#dialog_add_artist").modal("hide");
                        artistList();
                    }

                },
                error: function(data) {
                    console.log('Error:', data);
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

        $(".btn-create-album").click(function() {
            $('#releaseDate').val(new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0]);
            $("#addAlbumModal").modal("show");
            artistList();
        });

        $('#submitAlbum').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            $this.html(`<i class="fas fa-spinner fa-spin"></i> Loading...`);
            const albumTitle = $('#albumTitle').val();
            const albumArtist = $('#albumArtist').val();
            const albumGenre = $('#albumGenre').val();
            const albumGenreText = $('#albumGenre option:selected').text();
            const albumCover = $('#albumCover')[0].files[0];

            if (!albumTitle || !albumArtist || !albumGenre || !albumCover) {
                showNotification("Please fill in all required album information!", "error");
                $this.html(`<i class="fas fa-plus-circle mr-1"></i> Create Album`);
                return;
            }

            $('#submitBtn').attr('disabled', true);

            // Prepare data
            const formData = new FormData();
            formData.append('_token', $('input[name="_token"]').val());
            formData.append('title', albumTitle);
            formData.append('artist', albumArtist);
            formData.append('genre', albumGenre);
            formData.append('genreText', albumGenreText);
            formData.append('releaseDate', $('#releaseDate').val());
            formData.append('albumCover', albumCover);

            fetch('/addAlbum', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer yqeqnwkel1kenwmlrjkqnlendl'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        showNotification("Error creating music album", "error");
                        return;
                        //                        throw new Error('Error creating music album');
                    }
                    return response.json();
                })
                .then(data => {
                    $this.html(`<i class="fas fa-plus-circle mr-1"></i> Create Album`);
                    if (data.status == "success") {
                        showNotification("Album created successfully!", "success");
                        $("#form-album")[0].reset();
                        $("#addAlbumModal").modal("hide");
                        location.reload();

                    } else {
                        showNotification(data.message, "error");
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    console.error('Error:', error);
                })
                .finally(() => {
                    $('#submitBtn').attr('disabled', false);

                });
        });

        $('#imagePreview').click(function() {
            $('#albumCover').click();
        });
       
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
                        renderAlbums();
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
                        const processedAlbum = {
                            id: albumData.id,
                            name: albumData.album_name,
                            description: albumData.desc,
                            artist: albumData.artist,
                            genre: albumData.genre_name,
                            releaseDate: albumData.release_date,
                            coverImg: albumData.album_cover,
                            distributed: albumData.is_released,
                            status: albumData.status,
                            distroReleaseDate : albumData.distro_release_date,
                            username:albumData.username,
                            spotify_info:JSON.parse(albumData.spotify_info)
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

        function addSongToAlbum(songId, albumId) {
            return $.ajax({
                url: '/addSongToAlbum',
                type: 'GET',
                data: {
                    song_id: songId,
                    album_id: albumId
                },
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

        function handleAddSongsToAlbum() {
            if (selectedSongs.length === 0 || !currentAlbumId) {
                showNotification('Please select at least one song', 'warning');
                return;
            }

            // Hiển thị loading
            $('#add-songs-btn').html('<i class="fas fa-spinner fa-spin"></i> Adding...').prop('disabled', true);

            // Tạo mảng promises để xử lý các request
            const addPromises = selectedSongs.map(songId => {
                return addSongToAlbum(songId, currentAlbumId);
            });

            // Xử lý tất cả các request
            Promise.all(addPromises)
                .then(results => {
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

                            // Render lại album details (ở phía sau modal)
                            showAlbumDetails(currentAlbumId);

                            // Render lại danh sách album (ở phía sau modal)
                            renderAlbums();

                            // KHÔNG đóng modal

                            // Clear selected songs but keep filters
                            selectedSongs = [];
                            updateSelectedCount();

                            // Re-render available songs with current filters
                            renderAvailableSongs(currentGroupId);

                            // Hiển thị thông báo thành công
                            showNotification(`Added ${results.length} songs to album successfully`, 'success');
                        }
                    });
                })
                .catch(error => {
                    console.error('Error adding songs to album:', error);
                    showNotification('Failed to add songs to album. Please try again.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    $('#add-songs-btn').html('Add Songs').prop('disabled', false);
                });
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

                    // Hiển thị/ẩn dựa trên kết quả tìm kiếm
                    if (albumTitle.includes(searchTerm) ||
                        albumArtist.includes(searchTerm) ||
                        albumDate.includes(searchTerm) ||
                        albumSongs.includes(searchTerm) ||
                        albumStatus.includes(searchTerm) ||
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

            // Kiểm tra nếu không có album nào
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

                // Gán sự kiện cho nút tạo album
                $('.btn-create-first-album').click(function() {
                    $('#addAlbumModal').modal('show');
                });

                return; // Không cần tiếp tục xử lý phần còn lại của hàm
            }
            console.log(albums);
            // Phần code hiển thị danh sách albums (chỉ chạy khi có ít nhất 1 album)
            albums.forEach(album => {
                const songCount = album.songs.length;
                const distributeStatus = album.distributed == 1 ?
                    `<span class="badge badge-success album-status-badge">Distributed</span>` :
                    (album.distributed == 2 ?
                        `<span class="badge badge-warning album-status-badge">Pending Distribution</span>` :
                        `<span class="badge badge-secondary album-status-badge">Not Yet Distributed</span>`);


                // Định dạng ngày phát hành
                const releaseDate = new Date(album.releaseDate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                // Nút distribute đặt trên ảnh album
                const distributeButton = !album.distributed ?
                    `<button id="btn-quick-dis-${album.id}" class="distribute-button distribute-btn cur-poiter" data-album-id="${album.id}" title="Distribute this album">
                <i class="fas fa-solid fa-rocket"></i>
            </button>` : '';
                let albumCard = `
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card album-card" data-album-id="${album.id}">
                    <div class="album-cover-wrapper">
                        ${distributeStatus}
                        <img src="${album.coverImg}" class="card-img-top" alt="${album.name}">
                        ${distributeButton}
                    </div>
                    <div class="card-body">
                        <h6 class="album-title">${album.name}</h6>
                        <div class="album-meta">
                            <div class="album-meta-item">
                                <i class="fas fa-user"></i> ${album.artist}
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
                //                toggleDistribute(albumId);
                distribute(albumId);
            });

            // Thiết lập chức năng tìm kiếm sau khi render album xong
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
                        .edit-release-date-btn {
                            background: none;
                            border: none;
                            color: #007bff;
                            font-size: 14px;
                            padding: 0;
                            margin-left: 8px;
                            cursor: pointer;
                            transition: color 0.2s ease;
                        }

                        .edit-release-date-btn:hover {
                            color: #0056b3;
                        }

                        .edit-release-date-btn:focus {
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
        // Định dạng ngày phát hành
        const releaseDate = new Date(album.releaseDate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Chuẩn bị phần Spotify links nếu có dữ liệu
            let spotifyLinksHTML = '';

            if (album.spotify_info) {
                try {
                    // Parse spotify_info nếu nó là string
                    const spotifyInfo = typeof album.spotify_info === 'string' 
                        ? JSON.parse(album.spotify_info) 
                        : album.spotify_info;

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

        const albumInfoHTML = `
            <div class="album-cover-container">
                <img src="${album.coverImg}" class="album-cover-img" alt="${album.name}" id="current-album-cover">
            </div>

            <div class="album-info-panel">
                <div class="album-creator">
                    <img src="/images/avatar/${album.username}.jpg" class="user-avatar" alt="Creator avatar">
                    <span>Created by ${album.username}</span>
                </div>

                <div class="album-info-grid">
                    <div class="album-info-item">
                        <div class="album-info-label">Artist</div>
                        <div class="album-info-value">
                            <i class="fas fa-user"></i> ${album.artist || 'Various Artists'}
                        </div>
                    </div>

                    <div class="album-info-item">
                        <div class="album-info-label">Release Date</div>
                        <div class="album-info-value">
                            <i class="fas fa-calendar-alt"></i> 
                            <span id="release-date-display">${releaseDate}</span>
                            ${album.distributed !=1 ? 
                                `<button class="edit-release-date-btn ml-2" data-album-id="${album.id}" data-date="${album.releaseDate}">
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

                    <div class="album-info-item">
                        <div class="album-info-label">Songs</div>
                        <div class="album-info-value">
                            <i class="fas fa-music"></i> <span id="song-count">${album.songs ? album.songs.length : 0}</span>
                        </div>
                    </div>

                    <div class="album-info-item">
                        <div class="album-info-label">Status</div>
                        <div class="album-info-value">
                            <span class="detail-status-badge ${album.distributed==1 ? 'distributed' : album.distributed == 2?'badge-warning': 'not-distributed'}" id="current-album-status">
                                <i class="fas ${album.distributed ==1 ? 'fa-check-circle' : album.distributed == 2 ? 'fa-clock':'fa-times-circle' }"></i>
                                ${album.distributed == 1 ? 'Distributed' : album.distributed == 2 ? 'Pending Distribution':'Not Yet Distributed'}
                            </span>
                        </div>
                    </div>
                </div>
                                    ${spotifyLinksHTML}
            </div>
        `;

        return albumInfoHTML;
    }

        function showAlbumDetails(albumId) {
            currentAlbumId = albumId;

            // Hiển thị loading
            $('#album-details').show();
            $('#albums-container').hide();
            $('#album-details .card-body').html(
                '<div class="text-center my-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 col-md-12">Loading album details...</p></div>'
            );

            // Lấy thông tin chi tiết album
            fetchAlbumDetails(albumId, function(error, album) {
                if (error) {
                    $('#album-details .card-body').html(
                        '<div class="text-center my-5"><i class="fas fa-exclamation-triangle text-danger fa-2x"></i><p class="mt-2 col-md-12">Failed to load album details. Please try again.</p></div>'
                    );
                    console.error('Error fetching album details:', error);
                    return;
                }
                
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

                // Cập nhật header - Ẩn nút Add Songs nếu album đã distributed
                const headerContent = `
            <div class="album-detail-title">
                <button class="btn btn-back" id="back-to-albums">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h5 id="current-album-name">
                    ${album.name}
                    ${distributionIndicator}
                </h5>
                        
            </div>
            <div class="album-action-buttons">
                ${album.distributed == 1 ? 
                    `<button id="btn-spotify-scan" class="btn btn-action btn-spotify" data-album-id="${album.id}" onclick="scanSpotify(${album.id})">
                        <i class="fab fa-spotify"></i> Scan
                    </button>` : ''}
                 @if($is_admin_music)
                    ${album.distributed != 1 ?
                        `<button id="album-details-distribute-btn" class="btn btn-action btn-distribute-detail distribute-btn" data-album-id="${album.id}" onclick="distribute(${album.id})">
                               <i class="fas fa-solid fa-rocket"></i> Distribute
                            </button>
                            <button class="btn btn-action btn-add-songs" data-toggle="modal" data-target="#addSongsModal">
                                <i class="fas fa-plus"></i> Add Songs
                            </button>` : ''}     
                 @else
                    ${album.distributed != 1 && album.distributed != 2 ?
                        `<button id="album-details-distribute-btn" class="btn btn-action btn-distribute-detail distribute-btn" data-album-id="${album.id}" onclick="distribute(${album.id})">
                               <i class="fas fa-solid fa-rocket"></i> Distribute
                            </button>
                            <button class="btn btn-action btn-add-songs" data-toggle="modal" data-target="#addSongsModal">
                                <i class="fas fa-plus"></i> Add Songs
                            </button>` : ''}      
                 @endif          
 
            </div>
        `;

                // Cập nhật nội dung header
                $('.card-header .d-flex').html(headerContent);
                $('[data-toggle="tooltip"]').tooltip();

                // Cập nhật nội dung card body
                const albumInfoColumn = $('<div class="col-md-4 mb-4 album-info-container"></div>');
                albumInfoColumn.html(updateAlbumInfoHTML(album));

                const songsColumn = $('<div class="col-md-8 songs-container"></div>');
                songsColumn.html(`
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Songs List</h5>
                <form id="album-songs-search-form" class="search-container">
                    <div class="search-container" id="album-search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Search and press Enter..." id="album-songs-search">
                    </div>

                </form>
            </div>
            <div class="songs-list" id="album-songs-list">
                <!-- Album songs will be listed here -->
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
                    <div id="player-progress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                </div>
                <audio id="audio-element" src=""></audio>
            </div>
        `);

                // Cập nhật content
                $('#album-details .card-body').empty()
                    .append($('<div class="row h-100"></div>').append(albumInfoColumn).append(songsColumn));

                // Render danh sách bài hát
                renderAlbumSongs(album);

                // Thiết lập sự kiện cho nút Back
                $('#back-to-albums').off('click').on('click', function() {
                    // Ẩn chi tiết album và hiển thị danh sách album
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

                // Thiết lập sự kiện tìm kiếm bài hát
                setupAlbumSongSearch();
            });
        }

        // Function to render songs in an album
        function renderAlbumSongs(album) {
            const songsContainer = $('#album-songs-list');
            songsContainer.empty();

            if (!album.songs || !album.songs.length) {
                songsContainer.html(`
            <div class="text-center py-5 text-muted" id="empty-album-message">
                <i class="fas fa-music fa-3x mb-3"></i>
                <p class="w-100">This album has no songs yet</p>
                ${!album.distributed ?
                        `<button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#addSongsModal">
                                Add Songs Now
                            </button>` : ''}
            </div>
        `);
                $('#audio-player').addClass('d-none');
                return;
            }

            album.songs.forEach(song => {
//                const spotifyButton = song.spotify_id ? 
//                    `<a href="https://open.spotify.com/track/${song.spotify_id}" target="_blank" class="spotify-link mr-2" title="Listen on Spotify">
//                        <i class="fab fa-spotify"></i>
//                    </a>` : '';   
            const spotifyButton = song.spotify_id ? 
                `<div class="spotify-link mr-2 cur-poiter" title="Copy Spotify link" data-spotify-id="${song.spotify_id}">
                    <i class="fab fa-spotify"></i>
                </div>` : '';
                const songItem = `
            <div class="song-item d-flex justify-content-between align-items-center searchable-song-item" data-song-id="${song.id}">
                <div class="d-flex align-items-center">
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
                    ${!album.distributed ?
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

            // Only add click events for remove buttons if they exist (for non-distributed albums)
            if (!album.distributed) {
                // Add click event to remove song buttons
                $('.remove-song-btn').click(function(e) {
                    e.stopPropagation();
                    const songId = $(this).data('song-id');
                    handleRemoveSongFromAlbum(songId, album.id);
                });
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

            // Kiểm tra xem bài hát hiện tại có phải là bài hát đang được yêu cầu phát không
            if (currentAudio && currentAudio.id === song.id) {
                // Nếu đang phát, tạm dừng
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

            if (!audioElement.duration) return;

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

        // Function to toggle play/pause in the player
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

            if (!audioElement.duration) return;


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



        // Tìm kiếm qua tất cả bài hát trong modal
        $('.modal-song-item').each(function() {
            const songTitle = $(this).find('.modal-song-title').text().toLowerCase();
            const songArtist = $(this).find('.modal-song-artist').text().toLowerCase();
            const songGenre = $(this).data('genre');

            console.log(`Checking song: "${songTitle}" by "${songArtist}", genre: ${songGenre}`);

            // Kiểm tra cả từ khóa tìm kiếm và bộ lọc thể loại
            const matchesSearch = songTitle.includes(searchTerm) || songArtist.includes(searchTerm);
            const matchesGenre = selectedGenre === 'all' || songGenre === selectedGenre;

            if (matchesSearch && matchesGenre) {
                $(this).removeClass('d-none').addClass('d-flex');
                hasResults = true;
                //                console.log('  - Showing song');
            } else {
                $(this).removeClass('d-flex').addClass('d-none');
                //                console.log('  - Hiding song');
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

                if (!audioElement.duration) return;

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
        // Function to update selected songs count
        function updateSelectedCount() {
            $('#selected-count').text(selectedSongs.length);
        }

        // Function to add selected songs to current album
        function addSongsToAlbum() {
            if (selectedSongs.length === 0 || !currentAlbumId) return;

            const albumIndex = albums.findIndex(a => a.id === currentAlbumId);

            if (albumIndex === -1) return;

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
            // Cập nhật cấu trúc HTML của modal
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
                <!-- Genre buttons will be added dynamically -->
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
            <!-- Available songs will be listed here -->
        </div>
        
        <!-- Audio Player for modal -->
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
            // Remove previous event handlers to avoid duplicates
            $('.progress').off('click');

            // Add click event for seeking
            $(document).on('click', '.progress', function(e) {
                e.preventDefault();
                const progressBar = $(this);
                const audioElement = progressBar.closest('.audio-player').find('audio')[0];

                if (!audioElement || !audioElement.duration) return;

                const offset = e.pageX - progressBar.offset().left;
                const percent = offset / progressBar.width();
                const seekTime = percent * audioElement.duration;

                //                console.log(`Seeking to ${seekTime} seconds (${percent * 100}%)`);
                audioElement.currentTime = seekTime;
            });

            // Ensure player play button functionality works
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

            // Same for the modal player button
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
            // Kiểm tra xem đã có thông báo không
            if ($(container).find('.no-results-message').length === 0) {
                $(container).append(`<div class="no-results-message">${message}</div>`);
            }
        }

        function hideNoResultsMessage(container) {
            $(container).find('.no-results-message').remove();
        }

        // Hàm tìm kiếm trong album detail
        function performAlbumSongSearch() {
            const searchTerm = $('#album-songs-search').val().toLowerCase().trim();
            if (!searchTerm) {
                // Hiển thị tất cả bài hát
                $('#album-songs-list .song-item').removeClass('d-none').addClass('d-flex');
                hideNoResultsMessage('#album-songs-list');
                return;
            }

            let hasResults = false;

            // Tìm kiếm qua tất cả bài hát trong album
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

            // Hiển thị thông báo nếu không có kết quả
            if (!hasResults) {
                showNoResultsMessage('#album-songs-list', 'No songs match your search. Try different keywords.');
            } else {
                hideNoResultsMessage('#album-songs-list');
            }
        }

        // Hàm tìm kiếm trong modal Add Songs
        function performModalSongSearch() {
            const searchTerm = $('#modal-song-search').val().toLowerCase().trim();
            if (!searchTerm) {
                applyGenreFilter(); // Chỉ áp dụng bộ lọc thể loại nếu không có từ khóa tìm kiếm
                hideNoResultsMessage('#available-songs-list');
                return;
            }

            // Lấy thể loại đang được lọc
            const selectedGenre = $('.genre-filter-btn.active').data('genre');
            let hasResults = false;

            // Tìm kiếm qua tất cả bài hát trong modal
            $('.modal-song-item').each(function() {
                const songTitle = $(this).find('.modal-song-title').text().toLowerCase();
                const songArtist = $(this).find('.modal-song-artist').text().toLowerCase();
                const songGenre = $(this).data('genre');


                // Kiểm tra cả từ khóa tìm kiếm và bộ lọc thể loại
                const matchesSearch = songTitle.includes(searchTerm) || songArtist.includes(searchTerm);
                const matchesGenre = selectedGenre === 'all' || songGenre === selectedGenre;

                if (matchesSearch && matchesGenre) {
                    $(this).removeClass('d-none').addClass('d-flex');
                    hasResults = true;
                } else {
                    $(this).removeClass('d-flex').addClass('d-none');
                }
            });

            // Hiển thị thông báo nếu không có kết quả
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
                }
            });
        }
        
//        function scanSpotify(albumId) {
//            // Cập nhật trạng thái nút
//            const scanButton = $('#btn-spotify-scan');
//            scanButton.html('<i class="fas fa-spinner fa-spin"></i> Scanning...').addClass('scanning');
//
//            // Gọi API scan album
//            $.ajax({
//                url: `/scanAlbum?id=${albumId}`,
//                type: 'GET',
//                dataType: 'json',
//                success: function(response) {
//                    // Hiển thị thông báo thành công
//                    showNotification(response.message || 'Scan completed successfully', response.status || 'success');
//
//                    // Cập nhật lại thông tin album để lấy spotify_id mới (nếu có)
//                    fetchAlbumDetails(albumId, function(error, album) {
//                        if (!error && album) {
//                            // Render lại danh sách bài hát
//                            renderAlbumSongs(album);
//                        }
//                        // Khôi phục trạng thái nút
//                        scanButton.html('<i class="fab fa-spotify"></i> Scan').removeClass('scanning');
//                    });
//                },
//                error: function(xhr, status, error) {
//                    console.error('Error scanning album:', error);
//                    // Hiển thị thông báo lỗi
//                    showNotification('Failed to scan album. Please try again.', 'error');
//                    // Khôi phục trạng thái nút
//                    scanButton.html('<i class="fab fa-spotify"></i> Scan').removeClass('scanning');
//                }
//            });
//        } 

function scanSpotify(albumId) {
    // Cập nhật trạng thái nút
    const scanButton = $('#btn-spotify-scan');
    scanButton.html('<i class="fas fa-spinner fa-spin"></i> Scanning... 0%');
    scanButton.addClass('scanning');
    
    // Đóng SSE cũ nếu có
    if (window.spotifyScanEventSource) {
        window.spotifyScanEventSource.close();
    }
    
    // Tạo SSE connection
    const eventSource = new EventSource(`/scanAlbum?id=${albumId}`);
    window.spotifyScanEventSource = eventSource;
    
    // Xử lý sự kiện bắt đầu
    eventSource.addEventListener('start', function(e) {
        const data = JSON.parse(e.data);
        console.log('Scan started. Total songs:', data.totalSongs);
    });
    
    // Xử lý sự kiện tiến trình
    eventSource.addEventListener('progress', function(e) {
        const data = JSON.parse(e.data);
        
        // Cập nhật % trên nút
        scanButton.html(`<i class="fas fa-spinner fa-spin"></i> Scanning... ${data.progress}%`);
    });
    
    // Xử lý sự kiện hoàn thành
    eventSource.addEventListener('complete', function(e) {
        // Đóng kết nối
        eventSource.close();
        
        // Khôi phục nút và cập nhật giao diện 
        scanButton.html('<i class="fab fa-spotify"></i> Scan');
        scanButton.removeClass('scanning');
        
        // Hiển thị thông báo thành công
        showNotification('Spotify scan completed successfully', 'success');
        
        // Cập nhật lại danh sách bài hát để hiển thị Spotify ID mới
        fetchAlbumDetails(albumId, function(error, album) {
            if (!error && album) {
                renderAlbumSongs(album);
            }
        });
    });
    
    // Xử lý sự kiện lỗi
    eventSource.addEventListener('error', function(e) {
        let errorMessage = 'Error during scan';
        
        try {
            if (e.data) {
                const data = JSON.parse(e.data);
                errorMessage = data.message || errorMessage;
            }
        } catch (err) { }
        
        // Hiển thị thông báo lỗi
        showNotification(errorMessage, 'error');
        
        // Đóng kết nối
        eventSource.close();
        
        // Khôi phục nút
        scanButton.html('<i class="fab fa-spotify"></i> Scan');
        scanButton.removeClass('scanning');
    });
    
    // Xử lý lỗi kết nối
    eventSource.onerror = function() {
        // Đóng kết nối
        eventSource.close();
        
        // Hiển thị thông báo lỗi
        showNotification('Connection error during scan', 'error');
        
        // Khôi phục nút
        scanButton.html('<i class="fab fa-spotify"></i> Scan');
        scanButton.removeClass('scanning');
    };
}

        let currentModalAudio = null;
        $(document).ready(function() {
            // Biến toàn cục
            window.albums = [];
            window.currentAlbumId = null;
            window.selectedSongs = [];
            window.currentAudio = null;
            window.currentModalAudio = null;

            // Lấy danh sách album
            fetchAlbums();

            // Thiết lập modal Add Songs
            setupAddSongsModal();

            // Thiết lập kéo thả thanh progress
            setupProgressBarDrag();

            // Thiết lập sự kiện cho modal Add Songs
            $('#addSongsModal').on('show.bs.modal', function() {
                renderAvailableSongs();
            });

            // Sự kiện khi đóng modal Add Songs - reset trạng thái
            $('#addSongsModal').on('hidden.bs.modal', function() {
                selectedSongs = [];
                updateSelectedCount();
            });

            // Sự kiện thêm bài hát vào album
            $('#add-songs-btn').click(handleAddSongsToAlbum);

            // Thiết lập sự kiện tìm kiếm album
            setupAlbumSearch();

            // Thiết lập sự kiện ẩn/hiện các mục
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

            // Sự kiện khi bài hát kết thúc
            $('#audio-element').on('ended', function() {
                $('#player-play-btn').html('<i class="fas fa-play"></i>').removeClass('playing');
                if (currentAudio) {
                    $(`.play-song-btn[data-song-id="${currentAudio.id}"]`).html(
                        '<i class="fas fa-play"></i>').removeClass('playing');
                }
                currentAudio = null;
            });
            
            // Add the styles for edit button
            addReleaseDateEditStyles();

            // Set up the release date edit functionality
            setupReleaseDateEdit(); 
            
            
        // Handle file selection for album cover
        $('#albumCover').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html(`
                        <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                    `);
                };
                reader.readAsDataURL(file);
            }
        });
        
        $(document).on('click', '.album-spotify-link', function() {
            const spotifyUrl = $(this).data('spotify-url');

            // Copy to clipboard
            copyToClipboard2(spotifyUrl);

            // Show copied effect
            $(this).addClass('copied');
            setTimeout(() => {
                $(this).removeClass('copied');
            }, 2000);

            // Show notification
            const isArtist = $(this).find('i').hasClass('fa-user');
            showNotification(`Spotify ${isArtist ? 'artist' : 'album'} link copied to clipboard`, "success");
        });             

        });
    </script>
@endsection
