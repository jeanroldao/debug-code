<?php
global $dados_jsgame;

if (!empty($_GET['ajax'])) {
  unset($_GET['ajax']);
  //echo '{msg:"erro"}';
  
  //$fp = fopen('dados.db', 'w+');
  //while(flock($fp, LOCK_EX | LOCK_NB) == false);
  //@$dados = unserialize(stream_get_contents($fp));
  //@$dados = unserialize(file_get_contents('dados.db'));
  $dados = $dados_jsgame;
  if (!$dados) {
    $dados = array();
  }
  //$_POST += $dados;
  foreach ($dados as $n => $player) {
    if (empty($_GET[$n])) {
      $_GET[$n] = $player;
    }
    if (empty($_GET[$n]['time'])) {
      $_GET[$n]['time'] = time();
    }
    
    //testa o timeout do player
    if ((time() - $_GET[$n]['time']) > 5) {
      unset($_GET[$n]);
    }
  }
  /*
  if (empty($_POST['bot'])) {
    $_POST['bot'] = array(
      'name' => 'bot',
      'pos' => array(
        'x' => 15,
        'y' => 15
      )
    );
  }
  
  */
  
  //$_POST['bot']['pos']['x']--;
  //unset($_POST['bot']);
  
  //file_put_contents('dados.db', serialize($_GET));
  $dados_jsgame = $_GET;
  //fwrite(
  //flock($fp, LOCK_UN);
  echo json_encode($_GET);
  return;
}
?>
<style>
.player {
  position: absolute;
}
</style>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

var GameObj = function (name, imgsrc) {
  
  this.name = name || '';
  
  //pega a imagem do parametro ou usa uma padrão
  this.imgsrc = imgsrc || 'player.gif';
  
  //posição inicial
  this.pos = {x:10, y:10};
  
  
  
  //funcao de update para todos os objetos
  this.update = function () {
    if (!this.img) {
      var tag = '<div class="player" id="player_'+this.name+'"><img src="'+this.imgsrc+'" width="20" height="20" />'+this.name+'</div>';
      this.img = $(document.body).append(tag).find('#player_'+this.name)[0];
      if (!this.img) {
        document.write(tag);
        this.img = document.getElementById('player_'+this.name);
      }
    }
    this.img.style.top = this.pos.y + 'px';
    this.img.style.left = this.pos.x + 'px';
  }
  
  //destruidor do objeto, faz a limpeza de qualquer dados do objeto
  this.dispose = function () {
    $(this.img).remove();
  }
};

var LiveObj = function (name, imgsrc) {
  GameObj.apply(this, arguments);
}

var PlayerObj = function (name, imgsrc) {
  LiveObj.apply(this, arguments);
}
var name = '';
while (!name) {
  name = prompt('qual seu nome?', ''); 
}
var imgsrc = '';//prompt('url da imagem:', '');
var player = new PlayerObj(name, imgsrc);
//console.log(player);

var playerlist = {};
playerlist[player.name] = player;
//player.update();

(function() {
  var updater = arguments.callee;
  //eventos do update aqui
  
  var dados = {};
  //for (var n in playerlist) {
    var n = player.name;
    dados[n] = {
      'pos': player.pos,
      'name': player.name,
      'imgsrc': player.imgsrc
    };
  //}
  
  $.ajax({
    type: 'GET',
    url: 'php_jsgame.php?ajax=sim',
    data: dados,
    dataType: 'json',
    success: function(json){ 
      //console.log(json); 
      for (var n in json) {
        if (playerlist[n] == undefined) {
          playerlist[n] = new PlayerObj(json[n].name, json[n].imgsrc);
        }
        //console.log([playerlist[n].name, player.name]);
        if (playerlist[n].name != player.name) {
          playerlist[n].pos = json[n].pos;
        }
      }
      for (var n in playerlist) {
        if (json[n] == undefined) {
          playerlist[n].dispose();
          delete playerlist[n];
        }
      }
      updater();
    }//
  });  
  //console.log(playerlist);
  for (var n in playerlist) {
    playerlist[n].update();
  }
  //setTimeout(arguments.callee, 1000);
})();

document.onkeydown = function (e) { 
  e = e || window.event;
  var keyCode = e.keyCode || e.which;
  var arrow = {left: 37, up: 38, right: 39, down: 40 };
  
  switch (keyCode) {
    case arrow.left:
      player.pos.x--;
      break;
    case arrow.up:
      player.pos.y--;
      break;
    case arrow.right:
      player.pos.x++
      break;
    case arrow.down:
      player.pos.y++;
      break;
  }
  player.update();
};

</script>
