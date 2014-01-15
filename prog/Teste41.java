// aqui fica a mensagem!!!
import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste41 {

	public static void main(String[] args) throws Exception {
		try {
			System.out.println(new Scanner(Teste41.class.getClassLoader().getResourceAsStream("com.mysql.jdbc.LocalizedErrorMessages.properties")).nextLine());
		} catch (Exception e) {
			System.out.println(e);
		}
	}
}