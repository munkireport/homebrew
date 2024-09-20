<div id="homebrew-tab"></div>
<h2 data-i18n="homebrew.clienttab"></h2>
<div id="homebrew-msg" data-i18n="listing.loading" class="col-lg-12 text-center"></div>

<script>
$(document).on('appReady', function(){
    $.getJSON(appUrl + '/module/homebrew/get_data/' + serialNumber, function(data){

         // Check if we have data
        if (data.length == 0){
            $('#homebrew-msg').text(i18n.t('no_data'));
        } else {
            // Set count of homebrew items
            $('#homebrew-cnt').text(data.length);
            
            // Hide loading message
            $('#homebrew-msg').text('');

            // var skipThese = ['id','name','serial_number'];
            $.each(data, function(i,d){

                // Generate rows from data
                var rows = ''
                for (var prop in d){
                    if ((d[prop] == '' || d[prop] == null) && d[prop] !== 0){
                       // Do nothing for a blank entry
                       rows = rows
                    } 
                    else if((prop == 'built_as_bottle' || prop == 'installed_as_dependency' || prop == 'installed_on_request' || prop == 'poured_from_bottle' || prop == 'versions_bottle' || prop == 'keg_only' || prop == 'outdated' || prop == 'pinned' || prop == 'versions_devel' || prop == 'versions_head' || prop == 'deprecated') && d[prop] == 1){
                       rows = rows + '<tr><th>'+i18n.t('homebrew.'+prop)+'</th><td>'+i18n.t('yes')+'</td></tr>';
                    }
                    else if((prop == 'built_as_bottle' || prop == 'installed_as_dependency' || prop == 'installed_on_request' || prop == 'poured_from_bottle' || prop == 'versions_bottle' || prop == 'keg_only' || prop == 'outdated' || prop == 'pinned' || prop == 'versions_devel' || prop == 'versions_head' || prop == 'deprecated') && d[prop] == 0){
                       rows = rows + '<tr><th>'+i18n.t('homebrew.'+prop)+'</th><td>'+i18n.t('no')+'</td></tr>';
                    }

                    else if(prop == 'homepage'){
                       rows = rows + '<tr><th>'+i18n.t('homebrew.'+prop)+'</th><td><a href="'+d[prop]+'">'+d[prop]+'</a></td></tr>';
                    }
                    else if(prop == 'install_time'){
                       var date = new Date(d[prop] * 1000);
                       rows = rows + '<tr><th>'+i18n.t('homebrew.'+prop)+'</th><td><span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span></td></tr>';
                    }
                    else if(prop == 'name'){
                       // Skip the 'name' because we use it elsewhere
                       rows = rows
                    }
                    else {
                        rows = rows + '<tr><th>'+i18n.t('homebrew.'+prop)+'</th><td>'+d[prop]+'</td></tr>';
                    }
                }
                $('#homebrew-tab')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-beer'))
                        .append(' '+d.name))
                    .append($('<div style="max-width:1200px;">')
                        .addClass('table-responsive')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(rows))))
            })
        }
    });
});
</script>
