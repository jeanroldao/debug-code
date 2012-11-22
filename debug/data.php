<HTML>
   <BODY>
       <p align="center">
<font face="Verdana, Arial, Helvetica, sans-serif" 
size=1><span><font color="#003366" face="tahoma">  Sua Cidade / Seu Estado - 
<script language=JavaScript>
function dataPorExtenso(dia, mes, ano) {
  var year= ano;
  if (year<2000) {
    year += (year < 1900) ? 1900 : 0;
  }
  var daym= dia;
  if (daym < 10) {
    daym= '0'+daym;
  }
  var month=mes;
  var dayarray=new Array("Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado");
  var montharray=new Array(" de Janeiro de "," de Fevereiro de "," de Março de ","de Abril de ","de Maio de ","de Junho de","de Julho de ","de Agosto de ","de Setembro de "," de Outubro de "," de Novembro de "," de Dezembro de ");
  return (" "+daym+" "+montharray[month]+year+" ");
}

alert(dataPorExtenso(10, 10, 2010));
</script>
</font></span></font>
</p>
<p align="left">

   </BODY>
</HTML>