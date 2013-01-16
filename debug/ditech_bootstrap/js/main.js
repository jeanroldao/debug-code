$.ajax({
        type: "GET",
        url: 'http://jean/desenv/trunk/BL/BLbriefing.php',
        timeout: 20000,
        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15",
        //dataType: '',
        success: function(data){
          var tabela = [];
          var table = $('<table class="table table-condensed table-striped"></table>');
          var tr = $('<tr></tr>');
          
          $(data).find('#divFolderTab1 tr th.dataHeader,#divFolderTab1 tr th.dataHeaderSort').appendTo(tr);
          table.append(tr);
          
          $(data).find('tr.listOdd,tr.listEven').each(function(){
            var linha = [];
            var tr = $('<tr></tr>');
            $(this).find('td').each(function(){
              var td = $('<td></td>');
              //console.log($.trim($(this).text()));
              linha.push($.trim($(this).text()));
              td.text($.trim($(this).text()));
              //tr.append(td);
              tr.append(this);
            });
            tabela.push(linha);
            table.append(tr);
            //console.log($.trim($(this).find('td').text()));
          });
          $('#conteudo_tab').after(table);
          //console.table(tabela);
        }
});
//contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15"