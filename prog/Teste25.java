import java.util.*;
import java.io.*;
import java.sql.*;

public class Teste25 {
	
	public static void main(String[] args) throws Exception {
		ClassLoader loader = Teste25.class.getClassLoader();
        System.out.println(loader.getResource("java/lang/String.class"));
	}
}

