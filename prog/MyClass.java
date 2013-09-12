
public class MyClass {
	
	String nome;
	
	public MyClass(String nome) {
		this.nome = nome;
	}
	
	public void falar(String msg) {
		TesteInnerClass.dentro.da.classe.falarei();
		System.out.println(nome + ": " + msg + " (" + msg.length() + ")");
	}
}