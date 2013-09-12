import java.util.*;
import java.io.*;
import java.lang.reflect.InvocationHandler;  
import java.lang.reflect.Method;  
import java.lang.reflect.Proxy;  

public class Teste6 implements InvocationHandler {

	public static void main(String[] args) throws Exception {
		new Teste6().exec();
	}
	
	public void exec() throws Exception {
        FazTudo ft = (FazTudo) Proxy.newProxyInstance(Teste6.class.getClassLoader(),  
                new Class[] {FazTudo.class}, this);  
				
		ft.faz1();
		ft.faz2(1);
		ft.faz3("qualquer coisa, soh me deixe em paz... -_-");
	}
	
    public Object invoke(Object proxy, Method method, Object[] args) throws Throwable {  
		int length = args == null ? 0 : args.length;
		System.out.println(method);
		if (length == 0) {
			System.out.println(method.getName() + "()");
			throw new Exception("fail");
		} else {
			System.out.print(method.getName());
			System.out.print('(');
			for (int i = 0; i < length; i++) {
				if (i+1 == length) {
					System.out.print(args[i]);
				} else {
					System.out.print(args[i] + ", ");
				}
			}
			System.out.print(')');
			System.out.println();
		}
		return null;
    }  
}

interface FazTudo {
	void faz1() throws Exception;
	void faz2(int i);
	void faz3(int i, String oque);
	void faz3(String oque);
}
