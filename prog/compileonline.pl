#!/usr/bin/perl

package helloworld;
use strict;

my $command= $];
print "Perl version : ".$command."\n";

print "Hello World!\n";

#my $i = 0;
for (my $i = 0; $i < 10; $i++) {
  #print "$i\n";
  p($i);
}



sub p {
  my $n = $_[0];
  print "$n\n";
}


$::nome = "<jean>";

sub pnome {
  print $::nome;
  print "\n";
}

{
  local $::nome = "<none>";
  pnome();
}

pnome();

package pessoa;

sub new {
  my ($class, $nome) = @_;
  my $self = {};
  bless $self, $class;
  
  $self->nome($nome);
  
  $self->falar("novo");
  
  
  return $self;
}

sub nome {
  my ($self, $nome) = @_;
  
  if ($nome) {
    $self->{nome} = $nome;
  }
  
  return $self->{nome};

}

sub falar {
  my ($self, $msg) = @_;
  
  #print $self->nome;
  #print ": ";
  #print $msg;
  #print "\n";
  
  print "$self->{nome}: $msg\n";
  
  return $self;
}

sub frase {
  my ($self, $msg1, $msg2, $msg3) = @_;
  $self->falar($msg1);
  $self->falar($msg2);
  $self->falar($msg3);
}

sub pergunta {
  shift->frase(shift, shift, "?");
}

sub DESTROY {
  #my ($self) = @_;
  my $self = shift;
  $self->falar("DESTROY");
}

package main;

#teste de controle de scopo, perl é muito bom com isso
{
  my $p = new pessoa("jean");
  $p->falar("var 1")
    ->falar("var 2");
}

my $p2 = new pessoa("nop");
$p2->falar("ae!");
$p2->nome("nop1");
$p2->falar("opa!");
$p2->pergunta("tudo", "bem");
