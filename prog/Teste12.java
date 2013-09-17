import java.util.*;
import java.io.*;

public class Teste12 {
	
	public static void main(String[] args) throws Exception {
		System.out.println("start...");
		
		new Thread(new Tarefa(1)).start();
		new Thread(new Tarefa(2)).start();
		System.out.println("...end");
	}
	
	static class Tarefa implements Runnable{
			
		int id;
		
		public Tarefa(int id) {
			this.id = id;
		}
		public void run() {
			for (int i = 0;i < 10; i++) {
				System.out.println(id + "-" + i + "("+ Thread.currentThread().getId() +")");
				try {
					Thread.sleep(100);
				} catch (Exception e) {
					
				}
			}
		}
	}
}

