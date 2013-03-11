<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/lib.php');

$repo_id   = required_param('repo_id', PARAM_INT);  // Repository ID
$contextid = required_param('ctx_id', PARAM_INT);   // Context ID

$repo = repository::get_repository_by_id($repo_id, $contextid);

$ensembleUrl = $repo->options['ensembleURL'];
$evtype = $repo->options['evtype'];

?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ensemble Video External File Chooser</title>
    <link rel="stylesheet" href="css/jquery-ui/jquery-ui.min.css?v=1.8.3">
    <link rel="stylesheet" href="css/ev-script.css?v=20130304.1">
    <link rel="stylesheet" href="css/style.css?v=1">
  </head>
  <body>
    <form>
      <fieldset>
        <input id="content" name="content" type="hidden" />
        <input name="submit" class="submit" type="submit" value="Submit" />
      </fieldset>
    </form>
    <script src="js/jquery/jquery.min.js?v=1.8.3"></script>
    <script src="js/jquery-ui/jquery-ui.min.js?v=1.8.23"></script>
    <script src="js/jquery.cookie/jquery.cookie.js?v=1.3.1"></script>
    <script src="js/lodash/lodash.min.js?v=0.9.2"></script>
    <script src="js/backbone/backbone-min.js?v=0.9.2"></script>
    <script src="js/ev-script.min.js?v=20130310"></script>
    <script type="text/javascript">
        (function($) {

            'use strict';

            var wwwroot = window.parent.M.cfg.wwwroot,
                proxyPath = wwwroot + '/repository/ensemble/ext_chooser/proxy.php',
                ensembleUrl = '<?= $ensembleUrl ?>',
                type = '<?= $evtype ?>',
                app = new EV.EnsembleApp({
                    ensembleUrl: ensembleUrl,
                    authId: 'ev-moodle',
                    authPath: wwwroot,
                    pageSize: 100,
                    scrollHeight: 300,
                    proxyPath: proxyPath,
                    urlCallback: function(url) {
                        return proxyPath + '?request=' + encodeURIComponent(url) + '&repo_id=<?= $repo_id ?>&ctx_id=<?= $contextid ?>';
                    },
                }),
                $form = $('form'),
                $content = $('#content'),
                $submit = $('.submit').hide(),
                submitHandler = function(e) {
                    var settings = JSON.parse($content.val()),
                        content = _.extend({}, settings.content),
                        title = '',
                        thumbnail = '',
                        beforeSet = window.parent.tinymce.activeEditor.selection.onBeforeSetContent,
                        filepicker = window.parent.M.core_filepicker.active_filepicker;

                    title = content.Title || content.Name;
                    thumbnail = content.ThumbnailUrl || wwwroot + '/repository/ensemble/ext_chooser/css/images/logo.png';
                    // We don't need to persist content
                    delete settings['content'];
                    // Don't bother storing search either
                    delete settings['search'];
                    var callback = function(ed, arg) {
                        if (arg.content.indexOf(ensembleUrl) !== -1 && arg.content.indexOf('ev-thumb') === -1) {
                            var regex = new RegExp('>' + ensembleUrl.replace(/http(s)?:\/\//, '') + '[^<]*<');
                            arg.content = arg.content.replace(regex, '><img class="ev-thumb" title="' + title + '" src="' + thumbnail + '"/><');
                            // Once our content is updated we want to remove this handler
                            beforeSet.remove(callback);
                        }
                    };
                    beforeSet.add(callback);
                    filepicker.select_file({
                        // Lame hack so file is accepted by Moodle
                        title: title + '.avi',
                        source: ensembleUrl + '?' + $.param(settings),
                        thumbnail: thumbnail
                    });
                    // There's no reason to display the intermediate form so immediately submit
                    $('.fp-select-confirm', window.parent.document).click();
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
                if (type === 'video') {
                    app.handleField($('#content').parent(), new EV.VideoSettings(), '#content');
                } else if (type === 'playlist') {
                    app.handleField($('#content').parent(), new EV.PlaylistSettings(), '#content');
                }
            });

        }(jQuery));
    </script>
  </body>
</html>
