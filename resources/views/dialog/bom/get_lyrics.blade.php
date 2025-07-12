<div class="modal fade" id="dialog_get_lyrics" tabindex="-1" role="dialog" aria-labelledby="getLyricsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
<!--            <div class="modal-header">
                <h5 class="modal-title" id="getLyricsModalLabel">
                    <i class="fas fa-music"></i> Get Lyrics
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>-->
            <div class="modal-header">
                <div class="d-flex justify-content-sm-between align-items-center w-100">
                    <div>
                        <h5 class="modal-title mt-0 d-flex align-items-center">
                            <span id="created_by_avatar"></span>
                            <strong id="dialog-task-title">Get Lyrics</strong>
                        </h5>
                    </div>
                    <div class="">

                        <button
                            class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                            data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close"
                            data-dismiss="modal" style="padding-bottom: 0.6rem;padding-top: 0.6rem;z-index: 1001">
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
                <form id="getLyricsForm">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="lyricsUrl">
                            <strong>Letras.com URL</strong>
                            <small class="text-muted">(e.g., https://www.letras.com/karol-g/)</small>
                        </label>
                        <input type="url" 
                               class="form-control" 
                               id="lyricsUrl" 
                               name="page_url"
                               placeholder="https://www.letras.com/karol-g/"
                               required>
                        <small class="form-text text-muted">
                            Enter the artist URL from letras.com. The system will automatically append "mais_acessadas.html" to get the most accessed songs.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="getLyricsBtn">
                    <i class="fa fa-download"></i> Get Lyrics
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#dialog_get_lyrics .modal-body {
    padding: 2rem;
}

#dialog_get_lyrics .form-control {
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 10px 15px;
}

/*#dialog_get_lyrics .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}*/

/*#dialog_get_lyrics .modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}*/

/*#dialog_get_lyrics .modal-title {
    color: #495057;
    font-weight: 600;
}*/

/*#dialog_get_lyrics .btn {
    border-radius: 5px;
    padding: 8px 20px;
    font-weight: 500;
}

#dialog_get_lyrics .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

#dialog_get_lyrics .btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}*/
</style>