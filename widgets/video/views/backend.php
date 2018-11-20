<div id="<?= $this->context->id?>" class="VideoEditor">
    <div class="btn-toolbar gform">
    
        <div class="btn-group">
            <label class="btn btn-blue">
                <input type="checkbox" style="margin: 0;" class="select_all"/> Выбрать все
            </label>
			<?php if ($this->context->hasTitle):?>
            <span class="btn btn-default disabled edit_selected"><i class="icon-pencil"></i> Редактировать</span>
			<?php endif;?>
			
            <span class="btn btn-default disabled remove_selected"><i class="icon-remove"></i> Удалить</span>
		    <input value="" class="js-input-video-url" type="text" placeholder="Введите ссылку на видео с youtube, vimeo, coub" style="width: 400px;min-width: 400px;margin: 0 30px;">
		    <a class="btn btn-success js-btn-add-video"><i class="icon-add"></i> Добавить</a>		
        </div>
    </div>
    <hr/>
	
    <!-- Video Photos -->
    <div class="sorter">
        <div class="videos"></div>
        <br style="clear: both;"/>
    </div>
	
   <?php if ($this->context->hasTitle):?>
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
	<?php endif;?>


</div>

<style>
.VideoEditor {
    position: relative;
    border: 1px solid #DDD;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
}
.VideoEditor .videos {
	margin: 10px;
}

.VideoEditor .deleteVideo {
	display: none;
}

.VideoEditor .video:hover .deleteVideo {
	display: block;
}

.VideoEditor div.gform {
	padding:10px 6px;
    margin-left: 5px !important;
    margin-top: 5px !important;
}

.VideoEditor div.gform >.btn:first-child{
    margin-left: 4px;
}
.VideoEditor div.gform .btn{
    padding: 4px 8px;
}
.VideoEditor .video {
    position: relative;
    float: left;
    background-color: #fff;
    margin: 4px;
    height: 150px;
    width: 140px;

    display: block;
    padding: 4px;
    line-height: 1;
    border: 1px solid #DDD;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
}

.VideoEditor .video img {
    width: 140px;
    height: 85px;
}

.VideoEditor .video a {
    padding-left: 8px;
}

.VideoEditor .video .actions {
    float: right;

    position: absolute;
    bottom: 4px;
    right: 4px;
}

.VideoEditor hr {
    margin: 0 4px;
}

.VideoEditor .fileinput-button {
    position: relative;
    overflow: hidden;
    margin-left: 8px;
    margin-top: 4px;
    margin-bottom: 4px;
}

.VideoEditor .fileinput-button input {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    border: solid transparent;
    border-width: 0 0 100px 200px;
    opacity: 0;
    filter: alpha(opacity = 0);
    -moz-transform: translate(-300px, 0) scale(4);
    direction: ltr;
    cursor: pointer;
}

/* modal styles*/
.VideoEditor .preview {
    overflow: hidden;
    width: 200px;
    height: 156px;
    margin-right: 10px;
    overflow: hidden;
    float: left;
}

.VideoEditor .preview img {
    width: 200px;
}

.VideoEditor .video-editor {
    min-height: 156px;
    margin-bottom: 4px;
    padding: 4px;
    border: 1px solid #DDD;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    -moz-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
}

.video-editor form {
    margin-bottom: 0;
}

.VideoEditor .caption h5 {
    margin: 0;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
}
.VideoEditor .caption p {
    height: 3em;
    overflow: hidden;
    font-size: 13px;
}

/* fixed thumbnail sizes */
.VideoEditor.no-desc .video {
    height: 138px;
}

.VideoEditor.no-name .video {
    height: 160px;
}

.VideoEditor.no-name-no-desc .video {
    height: 120px;
}

.VideoEditor .video-preview {
    /*height: 88px;*/
    overflow: hidden;
}

/* item selection */
.VideoEditor .video-select {
    position: absolute;
    bottom: 8px;
    left: 8px;
}

.VideoEditor .video.selected {
    background-color: #cef;
    border-color: blue;
}

/* drag & drop hint above gallery */
.VideoEditor .overlay {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.VideoEditor .overlay-bg {
    position: absolute;
    top: 0;
    left: 0;
    background-color: #efefef;
    opacity: .5;
    width: 100%;
    height: 100%;
}

.VideoEditor.over .overlay {
    display: block;
}

.VideoEditor .drop-hint {
    background-color: #efefef;
    border: 2px #777 dashed;
    position: absolute;
    top: 50%;
    left: 50%;
    height: 100px;
    width: 50%;
    margin: -50px 0 0 -25%;
    text-align: center;
}

.VideoEditor .drop-hint-info {
    color: #777;
    font-weight: bold;
    font-size: 30px;
    margin-top: 35px;
    vertical-align: middle;
    display: inline-block;
}

</style>