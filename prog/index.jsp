<%-- 
    Document   : index
    Created on : Dec 6, 2013, 2:53:36 PM
    Author     : Jean_Roldao
--%>

<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <%!
    static class Pessoa {
        private static int c = 0;
        
        public static int getCount() {
            return c++;
        }
    }
    %>
    <body>
        <h1>Hello World!</h1>
        <%!
        private String getM() {
            return "MMM";
        }
        %>
        <b><% 
            Class cls = null;
            try {
                cls = Class.forName("IndexHelper");
            } catch (Exception e){
                out.print(e);
            }
            
            
        %>
        <%=cls.newInstance()%>
        </b>
        <%= this.getM() %>
        <%= Pessoa.getCount() %>
    </body>
</html>
