import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste34 {
	
	static class T1 implements Runnable {
		public void run() {
			PrintStream out = System.out;
			for (int i = 0; i < 10; i++) {
				out.println("T1: " + i);
			}
		}
	}
	
	static class T2 extends Thread {
		public void run() {
			PrintStream out = System.out;
			for (int i = 0; i < 10; i++) {
				out.println("T2: " + i);
			}
		}
	}
	
	public static void main(String[] args) throws Exception {
		new Thread().start();
		new Thread(new T1()).start();
		new T2().start();
		System.out.println("end!");
	}
	
}

