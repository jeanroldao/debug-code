import java.util.Arrays;

import java.util.concurrent.ForkJoinPool;
import java.util.concurrent.RecursiveAction;

public class Teste58 {
	
	public static void main(String[] args) {
		
	}
	
	public static <T> T createDynamicProxy(Class<T> type, Object o) {
        if (type.isAssignableFrom(o.getClass())) {
            return type.cast(o);
        } else {
            return null;
        }
	}
}