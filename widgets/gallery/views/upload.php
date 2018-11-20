<div id="<?= $id?>" class="GalleryEditor">
    <div class="btn-toolbar gform">
        <span class="btn btn-green fileinput-button">
            <i class="icon-plus icon-white"></i>Добавить…
			<input type="file" name="<?=$photoModelName?>[image]" class="afile" accept="image/*" multiple="multiple"/>
        </span>

        <div class="btn-group">
            <label class="btn btn-blue">
                <input type="checkbox" style="margin: 0;" class="select_all"/> Выбрать все
            </label>
            <span class="btn btn-default disabled edit_selected"><i class="icon-pencil"></i> Редактировать</span>
            <span class="btn btn-default disabled remove_selected"><i class="icon-remove"></i> Удалить</span>
        </div>
    </div>
    <hr/>
    <!-- Gallery Photos -->
    <div class="sorter">
        <div class="images"></div>
        <br style="clear: both;"/>
    </div>

    <!-- Modal window to edit photo information -->
    <div class="modal hide editor-modal">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>

            <h3>Редактирование информации</h3>
        </div>
        <div class="modal-body">
            <div class="form"></div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-success save-changes">
                Сохранить
            </a>
            <a href="#" class="btn" data-dismiss="modal">Отмена</a>
        </div>
    </div>
    <div class="overlay">
        <div class="overlay-bg">&nbsp;</div>
        <div class="drop-hint">
            <span class="drop-hint-info">Drop Files Here…</span>
        </div>
    </div>
    <div class="progress-overlay">
        <div class="overlay-bg">&nbsp;</div>
        <!-- Upload Progress Modal-->
        <div class="modal progress-modal">
            <div class="modal-header">
                <h3>Uploading images…</h3>
            </div>
            <div class="modal-body">
                <div class="progress progress-striped active">
                    <div class="bar upload-progress"></div>
                </div>
            </div>
        </div>
    </div>
</div>