
class php_test18_Menu {
	private static String[] myArray = {"eggs", "hamburgers", "tomato soup", "chicken pot pie", 
									   "spaghetti bolognese", "ice cream", "chocolate chip cookies", 
                                       "grilled sole"};

	public static void main(String[] args) {
		php_test18_Menu m = new php_test18_Menu();
		System.out.println(java.util.Arrays.asList(m.getMenu()));
	}

	public static String[] getMenu() {
		String[] menuArray = new String[3];
		for (int i = 0; i < 3; i++) {
			int r = (int)(Math.random() * (myArray.length - 1));
			menuArray[i] = myArray[r];
		}
		return menuArray;
	}
}