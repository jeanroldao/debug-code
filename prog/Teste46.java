// aqui fica a mensagem!!!
import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste46 {

	public static void main(String[] args) throws Exception {
		try {
			Properties p = new Properties();
			p.load(Teste46.class.getResourceAsStream("/Teste46.prop"));
			System.out.println(p);
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
}