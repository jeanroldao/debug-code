//index.js
//autor: jeanroldao@gmail.com

// hack IE se rodar direto do file system, 
// para funcionar a persistencia dos dados no IE usando localStorage
// precisa rodar a partir de um servidor http
// outros browsers funcionam com localStorage normalmente mesmo rodando sem http

var STORAGE = 'agencias';

function getFromStorage() {
	if (localStorage) {
		return localStorage.getItem(STORAGE);
	} else {
		return window.top.name;
	}
}

function saveToStorage(dados) {
	if (localStorage) {
		localStorage.setItem(STORAGE, dados);
	} else {
		window.top.name = dados;
	}
}

function carregarAgencias() {
	var agenciasJson = getFromStorage();
	if (agenciasJson) {
		return JSON.parse(agenciasJson)
	} else {
		saveToStorage('[]');
		return [];
	}
}

function sincronizarAgencias() {
	saveToStorage(JSON.stringify(agencias));
	agencias = carregarAgencias();
}

var agencias = carregarAgencias();

require(["dojo/on", "dojo/dom", "dijit/form/Button", "dojo/parser", "dijit/form/TextBox", "dojo/domReady!"], function(on, dom, Button, parser) {
	parser.parse();
	
	on(dom.byId('btn_nova_agencia'), 'click', function(){
		editarAgencia(null);
	});
	
	on(dom.byId('btn_salvar'), 'click', function(){
		salvarAgencia();
	});
	
	on(dom.byId('btn_cancelar'), 'click', function(){
		index();
	});
	
	on(dom.byId('btn_adicionar_operador'), 'click', function(){
		adicionarOperador();
	});
	
	index();
});

function index() {
	require(["dijit/form/Button", "dijit/registry", "dojo/parser", "dojo/domReady!"], function(Button, registry, parser) {
		$('#agencia_editar').hide();
		
		registry.byId('txt_nome').set('value', '');
		registry.byId('txt_operador').set('value', '');
		
		if (agencias.length > 0) {
			var tabela = $('#tbl_agencias');
			
			var linhas = tabela.find('tbody');
			linhas.find('tr:not(.hidden)').remove();
			
				
			$(agencias).each(function(i, agencia) {
				var linha = linhas.find('.hidden').clone().removeClass('hidden');
				linha.find('td:eq(0)').text(agencia.nome);
				linha.find('td:eq(1)').text(agencia.operadores.length);
				linha.find('td:eq(2)').append('<button data-dojo-type="dijit.form.Button" data-dojo-props="iconClass: \'dijitIconEdit\', onClick:function(){editarAgencia('+i+');}">Editar</button>');
				linha.find('td:eq(2)').append('<button data-dojo-type="dijit.form.Button" data-dojo-props="iconClass: \'dijitIconDelete\', onClick:function(){removerAgencia('+i+');}">Remover</button>');
				linhas.append(linha);
			});
		
			parser.parse();
			
			$('#tbl_agencias_vazia').hide();
			tabela.show();
		} else {
			$('#tbl_agencias').hide();
			$('#tbl_agencias_vazia').show();
		}
		
		$('#index').show();
	});
}

var agenciaEditada = {};
function editarAgencia(idAgencia) {
	$('#index').hide();
	if (idAgencia == null) {
		agenciaEditada = {id: null, nome:'', operadores:[]};
	} else {
		var agencia = agencias[idAgencia];
		agenciaEditada = {id: idAgencia, nome: agencia.nome, operadores: agencia.operadores.slice(0)};
	}
	require(["dijit/registry", "dojo/domReady!"], function(registry) {
		registry.byId('txt_nome').set('value', agenciaEditada.nome);
	});
	carregaOperadores();
	$('#agencia_editar').show();
}

function adicionarOperador() {
	var operador = $('#txt_operador').val();
	if (operador) {
		agenciaEditada.operadores.push(operador);
		
		require(["dijit/registry", "dojo/domReady!"], function(registry) {
			registry.byId('txt_operador').set('value', '');
		});
		
		carregaOperadores();
	}
}

function carregaOperadores() {
	var list = $('#list_operadores');
	
	list.children().remove();
	
	if (agenciaEditada.operadores.length == 0) {
		list.append('<div>(Sem operadores)</div>');
	} else {
		require(["dijit/form/Button", "dojo/parser", "dojo/domReady!"], function(Button, parser) {
			$(agenciaEditada.operadores).each(function(i, operador) {
				
				var linha = $('<div>');
				linha.append('<button data-dojo-type="dijit.form.Button" data-dojo-props="iconClass: \'dijitIconDelete\', onClick:function(){removerOperador('+i+');}">Remover</button>');
				linha.append($('<span>').text(operador));
				list.append(linha);
			});
			parser.parse();
		});
	}
}

function removerOperador(i) {
	agenciaEditada.operadores.splice(i, 1);
	carregaOperadores();
}

function salvarAgencia() {
	agenciaEditada.nome = $('#txt_nome').val();
	if (agenciaEditada.nome == '') {
		alert('Digite o nome da agência');
	} else {
		if (agenciaEditada.id == null) {
			agencias.push({nome: agenciaEditada.nome, operadores: agenciaEditada.operadores});
		} else {
			agencias[agenciaEditada.id].nome = agenciaEditada.nome;
			agencias[agenciaEditada.id].operadores = agenciaEditada.operadores;
		}
		agenciaEditada = {};
		sincronizarAgencias();
		index();
	}
}

function removerAgencia(idAgencia) {
	if (confirm('Remover agência '+agencias[idAgencia].nome+'?')) {
		agencias.splice(idAgencia, 1);
		sincronizarAgencias();
		index();
	}
}
