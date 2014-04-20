import java.util.Arrays;

public class Teste59 {
	
	public static void main(String[] args) {
		Object[] data = new Object[2];
		String[] ss = {"nada", "demais"};
		System.out.println(ss instanceof Object ? "true" : "false");
		System.out.println(ss instanceof Object[] ? "true" : "false");
		data[0] = ss;
		data[1] = 123;
		
		print_r(data);
		System.out.println();
	}
	
	public static void main0(String[] args) {
		String[] ss = {"nada", "demais"};
		Object[] os = ss;
		os[0] = 123456;
		
		String lol = ss[0];
		System.out.println(lol.getClass());
	}
	
	private static void print_r(Object o) {
		if (o == null || o instanceof Object[] == false) {
			System.out.print(o);
		} else {
			print_r("[");
			String comma = "";
			for (Object obj : (Object[]) o) {
				print_r(comma);
				comma = ",";
				print_r(obj);
			}
			print_r("]");
		}
	}
}