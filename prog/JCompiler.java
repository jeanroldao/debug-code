import java.io.File;
import java.io.FilenameFilter;
import java.io.IOException;
import java.nio.charset.Charset;
import java.util.Arrays;
import java.util.Locale;
import javax.tools.Diagnostic;
import javax.tools.DiagnosticCollector;
import javax.tools.JavaCompiler;
import javax.tools.JavaFileObject;
import javax.tools.StandardJavaFileManager;
import javax.tools.ToolProvider;
 
/**
 *
 * @author Babji Prashanth, Chetty
 */
public class JCompiler {
    public static void main(String[] args) throws Exception {
      System.out.println(System.getProperty("java.home"));
	  System.setProperty("java.home", "C:\\Program Files (x86)\\Java\\jdk1.6.0_41");
	  
	  File dir = new File("directory-path");
      File[] javaFiles = dir.listFiles(
              new FilenameFilter() {
                  public boolean accept(File file, String name) {
                      return name.endsWith("_test.java");
                  }
              });
      
	  javaFiles = new File[]{new File("JCompiler_test.java")};
	  //System.out.print("javaFiles == null? ");
	  //System.out.println(javaFiles == null);
      JavaCompiler javaCompiler = ToolProvider.getSystemJavaCompiler();
	  //System.out.print("javaCompiler == null? ");
	  //System.out.println(javaCompiler == null);
      DiagnosticCollector<JavaFileObject> diagnostics = new DiagnosticCollector<JavaFileObject>();
      StandardJavaFileManager fileManager = javaCompiler.getStandardFileManager(diagnostics, Locale.US, Charset.forName("UTF-8"));
      Iterable<? extends JavaFileObject> compilationUnits = fileManager.getJavaFileObjectsFromFiles(Arrays.asList(javaFiles));
      javaCompiler.getTask(null, fileManager, diagnostics, null, null, compilationUnits).call();
        
	  //System.out.println(diagnostics.getDiagnostics());
      for (Diagnostic diagnostic : diagnostics.getDiagnostics()) {
		  //System.out.print("diagnostic == null? ");
		  //System.out.println(diagnostic == null);
          System.out.format(diagnostic+" on line %s in %s\n", diagnostic.getLineNumber(), diagnostic.getSource().toString());
      }        
	  if (diagnostics.getDiagnostics().size() == 0) {
		((Runnable)Class.forName("JCompiler_test").newInstance()).run();
	  }
 
      fileManager.close();
    }
}