import java.io.*;
import java.util.*;

public class Teste28 {

	public static void main1(String[] args) {
		int i = 10;
		i += -1;
		System.out.println(i);
	}
	
	public static void main(final String[] args) {
		
		System.out.println("Enter Text >");
		
		String text = "";
		System.out.println(text.length());
 
		// create an event source - reads from stdin
		EventSource eventSource = new EventSource(args);
 
		// create an observer
		ResponseHandler responseHandler = new ResponseHandler();
 
		// subscribe the observer to the event source
		//eventSource.addObserver(responseHandler);
		
		eventSource.addObserver(new Observer() {
			public void update(Observable obj, Object arg) {
				System.out.println("Evento recebido: ("+args.length+") "+this+"->"+obj+"->"+arg);
			}		
			public String toString() {
				return "{Observer}";
			}
		});
 
		// starts the event thread
		Thread thread = new Thread(eventSource);
		thread.start();
	}
	 
	 
	static class EventSource extends Observable implements Runnable {
		private String[] args;
		private boolean running = true;
		
		public EventSource(String[] args) {
			this.args = args != null ? args : new String[0];
		}
		
		@Override
		public void run() {
			Scanner scan = new Scanner(System.in);
			
			System.out.println(args.length);
			for (String arg : args) {
				notifyObservers(arg);
			}
			args = null;
			
			while (running) {
				String response = scan.nextLine();
				notifyObservers(response);
			}
		}
		
		public void notifyObservers(Object arg) {
			if (arg.equals("exit")) {
				running = false;
			}
			setChanged();
			super.notifyObservers(arg);
		}
		
		public String toString() {
			return "{EventSource}";
		}
	}
	 
	static class ResponseHandler implements Observer {
		public void update(Observable obj, Object arg) {
			System.out.println("Received Response: " + arg );
		}
	}
	 

}

