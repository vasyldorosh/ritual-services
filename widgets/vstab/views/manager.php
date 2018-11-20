<div id="<?= $id?>" class="vsTabEditor GalleryEditor">
    <div class="btn-toolbar gform">
        <span class="btn btn-green js-vstab-create">
            <i class="icon-plus icon-white"></i>Добавить…
        </span>

        <div class="btn-group">
            <label class="btn btn-blue">
                <input type="checkbox" style="margin: 0;" class="select_all"/> Выбрать все
            </label>
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
    <div class="modal hide editor-modal image-modal">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>

            <h3></h3>
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
	

</div>

<style>

.image-modal .modal-body {overflow-y: auto !important;}

.image-container {
  overflow: hidden;
}
.image-container.rotate90,
.image-container.rotate270 {
}
.image-container img {
  transform-origin: top left;
  /* IE 10+, Firefox, etc. */
  -webkit-transform-origin: top left;
  /* Chrome */
  -ms-transform-origin: top left;
  /* IE 9 */
}
.image-container .rotate90 {
  transform: rotate(90deg) translateY(-100%);
  -webkit-transform: rotate(90deg) translateY(-100%);
  -ms-transform: rotate(90deg) translateY(-100%);
}
.image-container .rotate180 {
  transform: rotate(180deg) translate(-100%, -100%);
  -webkit-transform: rotate(180deg) translate(-100%, -100%);
  -ms-transform: rotate(180deg) translateX(-100%, -100%);
}
.image-container .rotate270 {
  transform: rotate(270deg) translateX(-100%);
  -webkit-transform: rotate(270deg) translateX(-100%);
  -ms-transform: rotate(270deg) translateX(-100%);
}

</style>