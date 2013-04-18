<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

$evtype = required_param('type', PARAM_TEXT);
$ensembleUrl = get_config('ensemble', 'ensembleURL');
$wwwroot = $CFG->wwwroot;
$path = parse_url($wwwroot, PHP_URL_PATH);
$path = ($path === '/' ? '' : $path);

?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ensemble Video External File Chooser</title>
    <link rel="stylesheet" href="css/jquery-ui/jquery-ui.min.css?v=1.8.3">
    <link rel="stylesheet" href="css/ev-script.css?v=20130409">
    <link rel="stylesheet" href="css/style.css?v=1">
  </head>
  <body>
    <form>
      <fieldset id="contentWrap">
        <input id="content" name="content" type="hidden" />
        <input name="submit" class="submit" type="submit" value="Save" style="display:none;" />
      </fieldset>
    </form>
    <script src="js/jquery/jquery.min.js?v=1.8.3"></script>
    <script src="js/jquery-ui/jquery-ui.min.js?v=1.8.23"></script>
    <script src="js/jquery.cookie/jquery.cookie.js?v=1.3.1"></script>
    <script src="js/lodash/lodash.underscore.min.js?v=1.1.1"></script>
    <script src="js/backbone/backbone-min.js?v=1.0.0"></script>
    <script src="js/ev-script/ev-script.js?v=20130417"></script>
    <script type="text/javascript">
        (function($) {

            'use strict';

            var wwwroot = '<?php echo $wwwroot ?>',
                proxyPath = wwwroot + '/repository/ensemble/ext_chooser/proxy.php',
                ensembleUrl = '<?php echo $ensembleUrl ?>',
                type = '<?php echo $evtype ?>',
                app = new EV.EnsembleApp({
                    ensembleUrl: ensembleUrl,
                    authId: 'ev-moodle',
                    authPath: '<?php echo $path . "/repository/ensemble/" ?>',
                    pageSize: 100,
                    scrollHeight: 300,
                    proxyPath: proxyPath,
                    urlCallback: function(url) {
                        return proxyPath + '?request=' + encodeURIComponent(url);
                    }
                }),
                $form = $('form'),
                $content = $('#content'),
                $submit = $('.submit').hide(),
                submitHandler = function(e) {
                    var settings = JSON.parse($content.val()),
                        content = _.extend({}, settings.content),
                        title = '',
                        thumbnail = '',
                        editor = window.parent.tinymce.activeEditor,
                        beforeSet = editor.selection.onBeforeSetContent,
                        filepicker = window.parent.M.core_filepicker.active_filepicker;

                    title = content.Title || content.Name;
                    thumbnail = content.ThumbnailUrl || wwwroot + '/repository/ensemble/ext_chooser/css/images/playlist.png';

                    // We don't need to persist content
                    delete settings['content'];
                    // Don't bother storing search either
                    delete settings['search'];

                    // Content to insert into editor
                    var html =
                        '<a class="mceNonEditable" href="' + ensembleUrl + '?' + $.param(settings) + '">' +
                        '  <img class="ev-thumb" title="' + title + '" src="' + thumbnail + '"/>' +
                        '</a>';

                    // Add our content directly into the editor...bypassing unnecessary/unused filepicker screens
                    editor.execCommand('mceInsertContent', false, html);

                    // Close the filepicker
                    filepicker.mainui.hide();

                    // Close tinymce popups
                    _.each(editor.windowManager.windows, function(w) {
                        var frameId = '', frame;
                        if (w.iframeElement) {
                            frameId = w.iframeElement.id;
                        }
                        frame = window.parent.document.getElementById(frameId);
                        if (frame && frame.contentWindow) {
                            editor.windowManager.close(frame.contentWindow);
                        }
                    });

                    e.preventDefault();
                };

            $submit.click(submitHandler);

            app.appEvents.bind('fieldUpdated', function($field, value) {
                if (value) {
                    $submit.show();
                } else {
                    $submit.hide();
                }
            });

            $(document).ready(function() {
                var $wrap = $('#content').parent();
                if (type === 'video') {
                    app.handleField($wrap[0], new EV.VideoSettings(), '#content');
                } else if (type === 'playlist') {
                    app.handleField($wrap[0], new EV.PlaylistSettings(), '#content');
                }
                app.appEvents.trigger('showPicker', $wrap.attr('id'));
            });

        }(jQuery));
    </script>
  </body>
</html>
