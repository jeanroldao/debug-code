import java.util.*;
import java.io.*;

public class JavaClassPhp {
	
	private File file;
	
	private Map<String, PhpOpCode[]> functions = new HashMap<String, PhpOpCode[]>();
	
	public JavaClassPhp(String filename) {
		int total_lines = countLines(filename);
		file = new File(filename);
		try {
			int current_line = 0;
			
			BufferedReader br = new BufferedReader(new FileReader(file));
			String current_class = "";
			
			while (current_line < total_lines) {
				//System.out.println(current_line + "/" + total_lines);
				
				boolean is_function = false;
				
				current_line++;
				String first_line = br.readLine();
				//System.out.println("first_line: (" + first_line + ")");
				
				if (first_line.substring(0, 5).equals("Class")) {
				
					current_class = first_line.substring(6) + ":";
					
					current_line++;
					first_line = br.readLine();
				}
				
				if (first_line.substring(0, 12).equals("End of class")) {
					current_class = "";
					
					current_line += 2;
					skipLines(br, 2);
					continue;
				}
				
				if (first_line.substring(0, 8).equals("Function")) {
					is_function = true;
					
					current_line++;
					first_line = br.readLine();
				}
				
				String fullfilename = first_line.substring(16);
				//System.out.println("file name: (" + fullfilename + ")");
				
				current_line++;
				//System.out.println(br.readLine());
				String function_name = current_class + br.readLine().substring(16);
				//System.out.println("function_name: " + function_name + "");
				
				current_line++;
				int ops_number = Integer.parseInt(br.readLine().substring(16));
				//System.out.println("ops number: " + ops_number + "");
				
				current_line += 3;
				skipLines(br, 3);
				
				PhpOpCode[] phpcode = new PhpOpCode[ops_number];
				
				for (int i = 0; i < ops_number; i++) {
					phpcode[i] = parsePhpOpcodeLine(br.readLine());
				}
				current_line += ops_number + 1;
				skipLines(br, 1);
				
				//System.out.println(current_line + "/" + total_lines);
				if (is_function) {
					current_line += 2;
					skipLines(br, 2);
				}
				
				functions.put(function_name.toLowerCase(), phpcode);
			}
			
			
			//System.out.println(current_line + "/" + total_lines);
			//runCode(phpcode);
			
			
			/*
			String line;
			while ((line = br.readLine()) != null) {
				parsePhpOpcodeLine(line);
			}
			*/
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	public int countLines(String filename) throws IOException {
		InputStream is = new BufferedInputStream(new FileInputStream(filename));
		try {
			byte[] c = new byte[1024];
			int count = 0;
			int readChars = 0;
			boolean empty = true;
			while ((readChars = is.read(c)) != -1) {
				empty = false;
				for (int i = 0; i < readChars; ++i) {
					if (c[i] == '\n') {
						++count;
					}
				}
			}
			return (count == 0 && !empty) ? 1 : count;
		} finally {
			is.close();
		}
	}	
	
	public void run() {
		//System.out.println(functions);//
		runCode(functions.get("(null)"), new PhpZVal[0], null);
	}
	
	public PhpZVal runCode(PhpOpCode[] code, PhpZVal[] args, PhpObject thisObj) {
	
		Map<String, PhpZVal> vars = new HashMap<String, PhpZVal>();
		List<PhpZVal> func_stack = new ArrayList<PhpZVal>();
		PhpObject obj = null;
		String method_to_call = null;
		String field_to_assign = null;
		
		int code_length = code.length;
		for (int i = 0; i < code_length; i++) {
			
			int operands_length = code[i].operands.length;
			for (int j = 0; j < operands_length; j++) {
				if (code[i].operands[j] instanceof PhpVariable) {
					//code[i].operands[j] = vars.get(((PhpVariable)code[i].operands[j]).index);
					((PhpVariable)code[i].operands[j]).vars = vars;
				}
			}
			
			switch(code[i].opName) {
				case ASSIGN:
					//System.out.print(Arrays.asList(code[i].operands));
					PhpVariable phpvar = (PhpVariable)code[i].operands[0];
					vars.put(phpvar.index, code[i].operands[1]);
					break;
				case ASSIGN_OBJ:
					if (code[i].operands.length == 2) {
						obj = code[i].operands[0].getObject();
						field_to_assign = code[i].operands[1].toString();
					} else {
						obj = thisObj;
						field_to_assign = code[i].operands[0].toString();
					}
					break;
				case ADD_CHAR:
					//System.out.println("{" + code[i].operands[1].getClass() + "}");
					if (code[i].operands[1] instanceof PhpIntValue) {
						code[i].operands[1] = new PhpStringValue(String.valueOf((char)code[i].operands[1].intval()));
					}
					//System.out.println();
					//System.exit(0);
					//break;
				case ADD_STRING:
				case ADD_VAR:
					StringBuilder phpstr = new StringBuilder();
					for (PhpZVal var : code[i].operands) {
						if (var instanceof PhpVariable) {
							phpstr.append(vars.get(((PhpVariable)var).index));
						} else {
							phpstr.append(var);
						}
					}
					vars.put(code[i].returns, new PhpStringValue(phpstr.toString()));
					//System.out.println(vars.get(code[i].returns));
					
					break;
				case DO_FCALL:
				case DO_FCALL_BY_NAME:
					//System.out.println(func_stack);
					//PhpZVal[] func_args = func_stack.toArray(new PhpZVal[0]);
					PhpZVal[] func_args = new PhpZVal[func_stack.size()];
					
					for (int argI = 0; argI < func_args.length; argI++) {
						//PhpVariable,PhpZVal
						PhpZVal zVal = func_stack.get(argI);
						if (zVal instanceof PhpVariable) {
							func_args[argI] = vars.get(((PhpVariable)zVal).index);
						} else {
							func_args[argI] = zVal;
						}
					}
					func_stack.clear();
					//System.out.println(Arrays.asList(code[i].operands));
					//System.out.println(functions);
					PhpZVal ret;
					if (obj == null) {
						ret = runCode(functions.get(code[i].operands[0].toString()), func_args, null);
					} else {
						ret = obj.invoke(method_to_call, func_args);
						obj = null;
						method_to_call = null;
					}
					//System.out.println(vars);
					vars.put(code[i].returns, ret);
					//System.out.println(vars);
					//System.exit(0);
					break;
				case ECHO:
					if (code[i].operands[0] instanceof PhpVariable) {
						//System.out.println(((PhpVariable)code[i].operands[0]).index);
						//System.out.println(((PhpVariable)code[i].operands[0]).vars);
					}
					System.out.print(code[i].operands[0].getValue());
					break;
				case FETCH_CLASS:
					vars.put(code[i].returns, code[i].operands[0]);
					break;
				case FETCH_OBJ_R:
					String field_to_fetch = null;
					if (code[i].operands.length == 2) {
						obj = code[i].operands[0].getObject();
						field_to_fetch = code[i].operands[1].toString();
					} else {
						obj = thisObj;
						field_to_fetch = code[i].operands[0].toString();
					}
					PhpZVal retval = obj.attr.get(field_to_fetch);
					//System.out.println("{"+retval+"}");
					obj = null;
					vars.put(code[i].returns, retval);
					break;
				case FREE:
					vars.put(((PhpVariable) code[i].operands[0]).index, null);
					break;
				case INIT_METHOD_CALL:
					obj = code[i].operands[0].getObject();
					method_to_call = code[i].operands[1].toString();
					break;
				case IS_SMALLER_OR_EQUAL:
					//System.out.println(Arrays.asList(code[i].operands[0], code[i].operands[1]));
					if (code[i].operands[0].intval() <= code[i].operands[1].intval()) {
						vars.put(code[i].returns, new PhpIntValue(1));
					} else {
						vars.put(code[i].returns, new PhpIntValue(0));
					}
					//System.out.println(vars.get(code[i].returns));
					//System.exit(0);
					break;
				case JMP:
					//System.out.println(code[i].operands[0].intval());
					i = code[i].operands[0].intval() - 1;
					break;
				case JMPZ:
					//System.out.println(code[i].operands[0].intval());
					if (code[i].operands[0].intval() == 0) {
						i = code[i].operands[1].intval() - 1;
					}
					//System.exit(0);
					break;
				case JMPZNZ:
					//System.out.println(code[i].operands[0].intval());
					if (code[i].operands[0].intval() == 0) {
						i = code[i].operands[1].intval() - 1;
					}
					//System.exit(0);
					break;
				case POST_INC:
					vars.put(code[i].returns, new PhpIntValue(code[i].operands[0].intval()));
					vars.put(((PhpVariable) code[i].operands[0]).index, new PhpIntValue(code[i].operands[0].intval() + 1));
					
					//System.out.println(((PhpVariable) code[i].operands[0]).index);
					//System.exit(0);
					break;
				case NEW:
					method_to_call = "__construct";
					obj = new PhpObject(code[i].operands[0].toString());
					vars.put(code[i].returns, obj);
					break;
				case NOP:
					break;
				case OP_DATA:
					PhpZVal varToSet = null;
					if (code[i].operands[0] instanceof PhpVariable) {
						varToSet = vars.get(((PhpVariable)code[i].operands[0]).index);
					} else {
						varToSet = code[i].operands[0];
					}
					//System.exit(0);
					obj.attr.put(field_to_assign, varToSet);
					obj = null;
					field_to_assign = null;
					break;
				case RECV:
					PhpZVal val = null;
					try {
						val = args[Integer.parseInt(code[i].returns.substring(1))];
					} catch (Exception e) {}
					
					vars.put(code[i].returns, val);
					//System.out.println(vars);
					//System.exit(0);
					break;
				case RETURN:
					//System.out.println(code[i].operands[0]);
					return code[i].operands[0];
				case SEND_VAR:
				case SEND_VAL:
					func_stack.add(code[i].operands[0]);
					break;
				default:
					System.out.println(code[i].opName + " not implemented");
					System.out.println(Arrays.asList(code[i].operands));
					System.exit(0);
			}
		}
		return null;
	}
	
	abstract static class PhpZVal {
		public abstract Object getValue();
		
		public String toString() {
			try {
				return getValue().toString();
			} catch (Exception e) {
				return "null";
			}
		}
		
		public int intval() {
			try {
				return Integer.parseInt(this.toString());
			} catch (Exception e) {
				return 0;
			}
		}
		
		public PhpZVal invoke(String method_name, PhpZVal[] args) {
			throw new RuntimeException(this.getClass() + " is not an object!");
		}
		
		public PhpObject getObject() {
			throw new RuntimeException(this.getClass() + " is not an object!");
		}
	}
	
	class PhpObject extends PhpZVal {
		public String class_name;
		public Map<String, PhpZVal> attr = new HashMap<String, PhpZVal>();
		
		public PhpObject(String class_name) {
			this.class_name = class_name;
		}
		
		public Object getValue() {
			return "[object " + class_name + "]";
		}
		
		public PhpZVal invoke(String method_name, PhpZVal[] args) {
			//System.out.println(functions);
			String func_name = (class_name + "::" + method_name).toLowerCase();
			//System.out.println(func_name);
			return runCode(functions.get(func_name), args, this);
		}
		
		public PhpObject getObject() {
			return this;
		}
	}
	
	static class PhpStringValue extends PhpZVal {
		public String str;
		
		public PhpStringValue() {
			this("");
		}
		
		public PhpStringValue(String str) {
			this.str = str;
		}
		
		public Object getValue() {
			return str;
		}
	}
	
	static class PhpIntValue extends PhpZVal {
		public Integer val;
		
		public PhpIntValue(Integer val) {
			this.val = val;
		}
		
		public Object getValue() {
			return val;
		}
	}
	
	static class PhpVariable extends PhpZVal {
		public String index;
		public Map<String, PhpZVal> vars;
		
		public PhpVariable(String index) {
			this.index = index;
		}
		
		public Object getValue() {
			return vars.get(index).getValue();
		}
		
		public PhpObject getObject() {
			return vars.get(index).getObject();
		}
	}
	
	static class PhpOpCode {
		//line     # *  op                           fetch          ext  return  operands
		public final int line;
		public final int opIndex;
		public final OPCode opName;
		public final String returns;
		public final PhpZVal[] operands;
		
		public PhpOpCode(int line, int opIndex, OPCode opName, String returns, PhpZVal[] operands) {
			this.line = line;
			this.opIndex = opIndex;
			this.opName = opName;
			this.returns = returns;
			this.operands = operands;
		}
	}
	
	enum OPCode { NOP, ADD, SUB, MUL, DIV, MOD, SL, SR, CONCAT, BW_OR, BW_AND, BW_XOR, BW_NOT, BOOL_NOT, BOOL_XOR, IS_IDENTICAL, IS_NOT_IDENTICAL, IS_EQUAL, IS_NOT_EQUAL, IS_SMALLER, IS_SMALLER_OR_EQUAL, CAST, QM_ASSIGN, ASSIGN_ADD, ASSIGN_SUB, ASSIGN_MUL, ASSIGN_DIV, ASSIGN_MOD, ASSIGN_SL, ASSIGN_SR, ASSIGN_CONCAT, ASSIGN_BW_OR, ASSIGN_BW_AND, ASSIGN_BW_XOR, PRE_INC, PRE_DEC, POST_INC, POST_DEC, ASSIGN, ASSIGN_REF, ECHO, PRINT, JMP, JMPZ, JMPNZ, JMPZNZ, JMPZ_EX, JMPNZ_EX, CASE, SWITCH_FREE, BRK, CONT, BOOL, INIT_STRING, ADD_CHAR, ADD_STRING, ADD_VAR, BEGIN_SILENCE, END_SILENCE, INIT_FCALL_BY_NAME, DO_FCALL, DO_FCALL_BY_NAME, RETURN, RECV, RECV_INIT, SEND_VAL, SEND_VAR, SEND_REF, NEW, INIT_NS_FCALL_BY_NAME, FREE, INIT_ARRAY, ADD_ARRAY_ELEMENT, INCLUDE_OR_EVAL, UNSET_VAR, UNSET_DIM, UNSET_OBJ, FE_RESET, FE_FETCH, EXIT, FETCH_R, FETCH_DIM_R, FETCH_OBJ_R, FETCH_W, FETCH_DIM_W, FETCH_OBJ_W, FETCH_RW, FETCH_DIM_RW, FETCH_OBJ_RW, FETCH_IS, FETCH_DIM_IS, FETCH_OBJ_IS, FETCH_FUNC_ARG, FETCH_DIM_FUNC_ARG, FETCH_OBJ_FUNC_ARG, FETCH_UNSET, FETCH_DIM_UNSET, FETCH_OBJ_UNSET, FETCH_DIM_TMP_VAR, FETCH_CONSTANT, GOTO, EXT_STMT, EXT_FCALL_BEGIN, EXT_FCALL_END, EXT_NOP, TICKS, SEND_VAR_NO_REF, CATCH, THROW, FETCH_CLASS, CLONE, RETURN_BY_REF, INIT_METHOD_CALL, INIT_STATIC_METHOD_CALL, ISSET_ISEMPTY_VAR, ISSET_ISEMPTY_DIM_OBJ, PRE_INC_OBJ, PRE_DEC_OBJ, POST_INC_OBJ, POST_DEC_OBJ, ASSIGN_OBJ, INSTANCEOF, DECLARE_CLASS, DECLARE_INHERITED_CLASS, DECLARE_FUNCTION, RAISE_ABSTRACT_ERROR, DECLARE_CONST, ADD_INTERFACE, DECLARE_INHERITED_CLASS_DELAYED, VERIFY_ABSTRACT_CLASS, ASSIGN_DIM, ISSET_ISEMPTY_PROP_OBJ, HANDLE_EXCEPTION, USER_OPCODE, ZEND_JMP_SET, ZEND_DECLARE_LAMBDA_FUNCTION, OP_DATA }
	
	static PhpOpCode parsePhpOpcodeLine(String opcode) {
		
		int i = 4;
		int line = 0;
		try {
			line = Integer.parseInt(opcode.substring(0, i).trim());
		} catch (Exception e) { }
		
		
		int opIndex = Integer.parseInt(opcode.substring(i, i += 6).trim());
		
		i += 6;
		OPCode opName = OPCode.valueOf(opcode.substring(i, i += 29).trim());
		
		i += 20;
		String returns = opcode.substring(i, i += 8).trim();
		
		PhpZVal[] operands = parsePhpOperands(opcode.substring(i));
		
		return new PhpOpCode(line, opIndex, opName, returns, operands);
	}
	
	static PhpZVal[] parsePhpOperands(String str) {
		if (str.length() == 0) {
			return new PhpZVal[0];
		}
		String[] operands_str_array = str.split(",");
		int size = operands_str_array.length;
		PhpZVal[] operands = new PhpZVal[size];
		//for (String operand : operands_str_array) {
		for (int i = 0; i < size; i++) {
			//System.out.println("i=" + i + ", size=" + size);
			String operand = operands_str_array[i].trim();
			
			try {
				operands[i] = new PhpIntValue(Integer.valueOf(operand));
				continue;//if success go for the next operand
			} catch(Exception e) {
				//in case of error continue trying to parse operand
			}
			
			//string operands are in single quotes
			if (operand.charAt(0) == '\'') {
				PhpStringValue phpString = new PhpStringValue();
				try {
					phpString.str = new java.net.URLDecoder().decode(operand.substring(1, operand.length() -1 ), "UTF-8");
				} catch (Exception e) {
					phpString.str = "{error decoding string}";
				}
				//System.out.println(phpString.str);
				operands[i] = phpString;
			
			//php variables
			} else if (operand.charAt(0) == '!'
					|| operand.charAt(0) == '$'
					|| operand.charAt(0) == '~'
					|| operand.charAt(0) == ':') {
				//operands[i] = PhpVariable.createVariableFromString(operand);
				operands[i] = new PhpVariable(operand);
			} else if (operand.equals("null")) {
				operands[i] = null;
			} else if (operand.substring(0,2).equals("->")) {
				PhpStringValue phpString = new PhpStringValue(operand.substring(2));
				operands[i] = phpString;
			} else {
				System.out.println(operand);
				System.out.println("Error! Operand not recognizable ("+i+")");
				System.exit(0);
			}
		}
		//System.out.println(Arrays.asList(operands));
		return operands;
	}
	
	/*
	/ * !0 * /
	static class PhpCompiledVar extends PhpZVal {
		public int index;
	}
	/ * $0 * /
	static class PhpVar extends PhpZVal {
		public int index;
	}
	/ * ~0 * /
	static class PhpRegister extends PhpZVal {
		public int index;
	}
	
	*/
	
	static void skipLines(BufferedReader br, int lines) throws IOException {
		for (int i = 0; i < lines; i++) {
			br.readLine();
		}
	}
	
	public static void main(String[] args) throws Exception{
		//System.out.println(Arrays.asList(args));
		JavaClassPhp page = new JavaClassPhp(args[0]);
		page.run();
	}
}

