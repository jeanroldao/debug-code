
import java.io.*;
import java.util.*;

public final class ClassReader implements Serializable, Closeable {
	
	//private static final String INPUT_FILE = "Teste.class";
	private static final String INPUT_FILE = "ClassReader.class";
	//private static final String INPUT_FILE = "java_euler.class";
	
	private static final int idade = 24;
	
	private static DataInputStream input;
	private static Map<Short, Object> constant_pool = new HashMap<Short, Object>();

	public static void main(String[] args) throws Exception {
		
		try {
			File file = new File(INPUT_FILE);
			FileInputStream fileInputStream = new FileInputStream(file);
			input = new DataInputStream(fileInputStream);
					
			//magic number test
			if(0xCAFEBABE != input.readInt()) {
				System.out.println("not a java class!");
				System.exit(1);
			}
			System.out.println("minor version: " + input.readShort());
			System.out.println("major version: " + input.readShort());
			
			short constant_pool_count = input.readShort();
			System.out.println("constant_pool_count = " + constant_pool_count);
			
			for (short i = 1; i < constant_pool_count; i++) {
				byte byte_tag = input.readByte();
				System.out.print(i + ": tag=" + byte_tag+": ");
				
				switch (byte_tag) {

					//??? Asciz?
					case 0:
						short size0 = input.readShort();
						byte[] str0 = new byte[size0];
						input.readFully(str0);
						System.out.print("Asciz: " + new String(str0));
						break;
					
					//utf8
					case 1:
						String utf = input.readUTF();
						constant_pool.put(i, utf); 
						System.out.print("utf8 string: " + utf);
						break;
				
					//int
					case 3:
						int intg = input.readInt();
						constant_pool.put(i, intg); 
						System.out.print("int: " + intg);
						break;
					
					//long
					case 5:
						long longNum = input.readLong();
						constant_pool.put(i, longNum); 
						
						//uses 2 indexes
						i++;
						System.out.print("long: " + longNum);
						break;
				
					//Class
					case 7:
						short shortNum = input.readShort();
						constant_pool.put(i, shortNum); 
						System.out.print("Class: utf8 index=" + shortNum);
						break;

					//String
					case 8:
						short strIndex = input.readShort();
						constant_pool.put(i, strIndex); 
						System.out.print("String: utf8 index=" + strIndex);
						break;
					
					//field ref
					case 9:
						short[] fieldRef = {input.readShort(), input.readShort()};
						constant_pool.put(i, fieldRef); 
						System.out.print("field: Class index=" + fieldRef[0]);
						System.out.print(", Name and Type index=" + fieldRef[1]);
						break;
					
					//method ref
					case 10:
						short[] methRef = {input.readShort(), input.readShort()};
						constant_pool.put(i, methRef); 
						System.out.print("method: Class index=" + methRef[0]);
						System.out.print(", Name and Type index=" + methRef[1]);
						break;

					//interface
					case 11:
						short[] interfRef = {input.readShort(), input.readShort()};
						constant_pool.put(i, interfRef); 
						System.out.print("interface: Class index=" + interfRef[0]);
						System.out.print(", Name and Type index=" + interfRef[1]);
						break;

						//name and type descriptor
					case 12:
						short[] descRef = {input.readShort(), input.readShort()};
						constant_pool.put(i, descRef); 
						System.out.print("name and type descriptor: name index=" + descRef[0]);
						System.out.print(", descriptor index=" + descRef[1]);
						break;
						
					default:
						System.out.println("unknown byte_tag");
						System.exit(1);
						return;
				}
				System.out.println();
			}
			
			short flag = input.readShort();
			System.out.print(String.format("flag %s", flag));
			flags(flag);
			
			System.out.println(String.format("this class: %s", getClassName(input.readShort())));
			
			System.out.println(String.format("super class: %s", getClassName(input.readShort())));
			
			short interfaces_count = input.readShort();
			System.out.println(String.format("interfaces: %s", interfaces_count));
			
			for (int i = 0; i < interfaces_count; i++) {
				System.out.println(String.format("%s: %s", i+1, getClassName(input.readShort())));
			}
			
			short fields = input.readShort();
			System.out.println("fields: " + fields);
			
			for (int i = 0; i < fields; i++) {
				flags(input.readShort());
				System.out.println("field name: " + getString(input.readShort()));
				System.out.println("descriptor name: " + getString(input.readShort()));
				
				short attr_count = input.readShort();
				System.out.println("atributes count: " + attr_count);
				attr(attr_count);
				
			}
			
		} catch (Exception e) {
			throw new Exception(e);
		}
	}
	
	static void attr(short count) throws Exception {
		for (int i = 0; i < count; i++) {
			//System.out.println("attribute name index: " + input.readShort());
			System.out.println("attribute name: " + getString(input.readShort()));
			
			int attr_length = input.readInt();
			System.out.println("attribute length: " + attr_length);
			if (attr_length == 2) {
				System.out.println("attribute value: " + (input.readShort()));
			} else {
				input.skipBytes(attr_length);
			}
		}
	}
	
	static String getString(short i) {
		return (String) constant_pool.get(i);
	}
	
	static String getClassName(short i) {
		i = (Short) constant_pool.get(i);
		return getString(i);
	}
	
	static String getDescriptorName(short i) {
		short[] desc = (short[]) constant_pool.get(i);
		return getString(desc[0]);
	}
	
	static void flags(short flag) {
		if ((flag & 0x0001) == 0x0001) {
			System.out.print(" public");
		}
		if ((flag & 0x0010) == 0x0010) {
			System.out.print(" final");
		}
		if ((flag & 0x0020) == 0x0020) {
			System.out.print(" super");
		}
		if ((flag & 0x0200) == 0x0200) {
			System.out.print(" interface");
		}
		if ((flag & 0x0400) == 0x0400) {
			System.out.print(" abstract");
		}
		if ((flag & 0x1000) == 0x1000) {
			System.out.print(" synthetic");
		}
		if ((flag & 0x2000) == 0x2000) {
			System.out.print(" annotation");
		}
		if ((flag & 0x4000) == 0x4000) {
			System.out.print(" enum");
		}
		System.out.println();
	}
	
	public void close() {}
}

/*
struct Class_File_Format {
   u4 magic_number;   
 
   u2 minor_version;   
   u2 major_version;   
 
   u2 constant_pool_count;   
 
   cp_info constant_pool[constant_pool_count - 1];
 
   u2 access_flags;
 
   u2 this_class;
   u2 super_class;
 
   u2 interfaces_count;   
 
   u2 interfaces[interfaces_count];
 
   u2 fields_count;   
   field_info fields[fields_count];
 
   u2 methods_count;
   method_info methods[methods_count];
 
   u2 attributes_count;   
   attribute_info attributes[attributes_count];
}
*/