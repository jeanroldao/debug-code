
class RefTest {

	public static void main(String[] args) {
		Object obj = new Object() {
			public String toString() {
				return new String(new char[]{'o', 'i'});
			}
		};
		System.out.println(obj.toString().intern());
	}
}