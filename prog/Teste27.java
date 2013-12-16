public class Teste27 {
	public static void method() {
	}
	public static void runTest() {
		long before;
		long after;
		// First, figure out the time for an empty loop
		before = System.currentTimeMillis();
		for (int index = 0; index < 1*1000*1000; index += 1) {
		}
		after = System.currentTimeMillis();
		long loopTime = after - before;
		System.out.println("Loop time: " +
						   Long.toString(loopTime) +
						   " milliseconds");
		// Then time the method call in the loop
		before = System.currentTimeMillis();
		for (int index = 0; index < 1*1000*1000; index += 1) {
			method();
		}
		after = System.currentTimeMillis();
		long methodTime = after - before;
		System.out.println("Method time: " +
						   Long.toString(methodTime) +
						   " milliseconds");
		System.out.println("Method time - Loop time: " +
						   Long.toString(methodTime - loopTime) +
						   " milliseconds");
	}
	public static void main(String[] arg) {
		// Warm up the virtual machine, and time it
		runTest();
		runTest();
		runTest();
	}
}