//var alert = function (msg) {print(msg);};
var alert = function (msg) {java.lang.System.out.println(msg);};

function Arma() {
  var nome = '';
  var poder = '';
  var dono = null;
  
  this.getNome = function() {
    return nome;
  }
  this.setNome = function(n) {
    nome = n;
  }
  
  this.getPoder = function() {
    return poder;
  }
  this.setPoder = function(p) {
    poder = p;
  }
  
  this.getDono = function() {
    return dono;
  }
  this.setDono = function(d) {
    if (dono != null && dono != d) {
	  dono.setArma(null);
	}
	dono = d;
  }
}

function Guerreiro() {
  
  var nome = '';
  var vida = 0;
  var arma = null;
  
  this.getNome = function() {
    return nome;
  }
  this.setNome = function(n) {
    nome = n;
  }
  
  this.getVida = function() {
    return vida;
  }
  this.setVida = function(v) {
    vida = v;
  }
  
  this.levarDano = function(v) {
    vida -= v;
	this.vefificaVida();
  }
  
  this.vefificaVida = function() {
    if (this.estaVivo() == false) {
	  alert(nome + ' morreu x.x');
	}
  }
  
  this.estaVivo = function () {
    return vida > 0;
  }
  
  this.getArma = function() {
    return arma;
  }
  this.setArma = function(/* Arma */ a) {
    arma = a;
	if (a != null) {
	  a.setDono(this);
	}
  }
  
  this.atacar = function(/* Guerreiro */alvo) {
    if (!arma) {
	  throw 'sem arma, não pode atacar';
	}
	alvo.levarDano(arma.getPoder());
  }
}


function criarArma(nome, poder) {
  var arma = new Arma();
  arma.setNome(nome);
  arma.setPoder(poder);
  
  return arma;
}

function criarJogador(nome, vida, arma) {
  var g = new Guerreiro();
  g.setNome(nome);
  g.setVida(vida);
  g.setArma(arma);
  
  return g;
}

var espada1 = criarArma('espada', 1200);
var espada2 = criarArma('espada', 200);
var j1 = criarJogador('j1', 1000, espada1);
var j2 = criarJogador('j2', 1000, espada2);

j1.atacar(j2);
