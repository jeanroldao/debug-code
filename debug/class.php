<script language="php"> 

class a {
  function b() {
    $_this = &$this;
    $_this->a = (object) array('123');
    
  }
}
</script>

<? $a = new a   ?>
<? var_dump($a) ?>
<? $a->b()      ?>
<? var_dump($a) ?>