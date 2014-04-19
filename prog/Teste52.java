import java.io.*;
import java.util.*;
import java.lang.reflect.*;
import java.rmi.Remote;
import java.rmi.RemoteException;
import java.rmi.server.UnicastRemoteObject;
import java.rmi.registry.Registry;
import java.rmi.registry.LocateRegistry;

public abstract class Teste52 {
	
	public static interface Hello extends Remote {
		String sayHello() throws RemoteException;
	}
	
	public static class Server implements Hello {
		public String sayHello()  throws RemoteException {
			return "hello, world...";
		}
	}
	
	public static void main(String[] args) throws Exception {
		if (args.length > 0 && args[0].equals("server")) {
			Server obj = new Server();
			Hello stub = (Hello) UnicastRemoteObject.exportObject(obj, 0);
			
			Registry registry = LocateRegistry.getRegistry();
			registry.bind("hello", stub);
			
			System.out.println("server ready");
		}
	}
	
}