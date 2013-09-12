import java.util.*;
import java.io.*;

public class Teste2 {

	public static void main(String[] args) throws Exception {
		
		Integer[] li = {1,2,3};
		Long[] ll = new Long[li.length];
		for (int i = 0; i < li.length; i++) {ll[i] = (long)li[i];}
		//System.out.println(ll.length);
		for (Long L : ll) {
			System.out.println(L.getClass());
			System.out.println(L);
		}
		
	}
	
}

