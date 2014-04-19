import java.io.*;
import java.util.*;
import java.net.*;
import java.lang.reflect.*;

public class Teste56 {
	
	public static void main(String[] args) throws Exception {
        try {
			Node<String> lista = new Node<String>();
			
			for (int n = 0; n < 10000; n++) {
				String v = "Nome" + n;
				System.out.println("add " + v);
				lista.add(v);
			}
			
			System.out.println("size: " + lista.size());
			for (int i = 0; i < lista.size(); i++) {
				System.out.println("get " + lista.get(i));
			}
		} catch (Exception e) {
			System.out.println(e.getMessage());
		} 
	}
	
	static class Node<T> {
		
		T value;
		Node<T> next;
		int size = 0;
		
		public void add(T o) {
			if (size == 0) {
				value = o;
			} else {
				if (next == null) {
					next = new Node<T>();
				}
				next.add(o);
			}
			size++;
		}
		
		public T get(int i) {
			if (i == 0) {
				return value;
			} else {
				return next.get(i - 1);
			}
		}
		
		public int size() {
			return size;
		}
	}
}	
