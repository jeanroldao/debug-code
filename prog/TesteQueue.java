import java.util.*;
import java.io.*;
import java.util.concurrent.LinkedBlockingQueue;

public class TesteQueue {
	
	static Scanner scan = new Scanner(System.in);
	
	static int records = 0;
	
	static LinkedBlockingQueue<String> fila = new LinkedBlockingQueue<String>();
	
	static List<Consumer> allThreads = new ArrayList<Consumer>();
	
	static class Consumer extends Thread {
		
		static int increment = 0;
		public static int producers = 0;
		
		int id;
		
		public boolean stop = false;
		
		public Consumer() {
			this.id = ++increment;
		}
		
		public void run() {
			System.out.println("consumer " + id + " start");
			while (!stop) {
				try {
					String msg = fila.take();
					System.out.println(id + ": " + msg + " (" + fila.size() + ") (" + (++records) + ") (producers="+producers+")");
					Thread.sleep(100);
				} catch (Exception e) {
					System.out.println("erro na fila: " + e.getMessage());
				}
			}
		}
	}
	
	public static void main(String[] args) throws Exception {
		System.out.println("main start");
		
		List<Consumer> ts = new ArrayList<Consumer>();
		

		Consumer c = new Consumer();
		c.start();
		ts.add(c);
		allThreads.add(c);

		
		
		String msg; 
		while ((msg = scan.nextLine()).length() != 0) {
			if ("add".equals(msg)) {
				Consumer t = new Consumer();
				t.start();
				ts.add(t);
			} else if ("re".equals(msg)) {
				Consumer t = ts.get(0);
				t.stop = true;
				//t.interrupt();
				ts.remove(t);
			} else {
				process(msg);
			}
		}
		
		for (Consumer t : allThreads) {
			t.stop = true;
			t.interrupt();
		}
	}
	
	static void process(final String msg) {
		Consumer t = new Consumer() {
			public void run() {
				Consumer.producers++;
				try {
					for (int i = 0; i < 100 && !stop; i++) {
						Thread.sleep(500);
						fila.put(msg+"("+i+")");
					}
				} catch (Exception e) {
					System.out.println("erro na fila: " + e.getMessage());
				}
				Consumer.producers--;
			}
		};
		t.start();
		allThreads.add(t);
	}
	
}

