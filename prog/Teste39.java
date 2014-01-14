import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste39 {

	public static void main(String[] args) throws Exception {
		new Thread(new Runnable(){
			public void run() {
				for (int i = 0; i < 100; i++) {
					System.out.println("from thread: " + i);
					try {
						Thread.sleep(1);
					} catch (Exception e) {}
				}
			}
		}).start();
		for (int i = 0; i < 100; i++) {
			System.out.println("from main: " + i);
			try {
				Thread.sleep(1);
			} catch (Exception e) {}
		}
	}
}