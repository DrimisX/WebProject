<%@ language="SQLRPGLE" %>
<%
H decedit('0.') datFmt(*USA)
D startRow        C                   1
D maxRows         C                   999
D sqlCmd          S           8192    varying

/free
setContentType('application/json; charset=utf-8');

sqlCmd = ('            				+
	SELECT book_id, book_title, LEFT(book_plot,40), author_last, author_first, author_middle +
	FROM books +
	JOIN book_authors USING(book_id) +
	JOIN authors USING(author_id) +
	FOR READ ONLY
');

SQL_Execute(
	I_EXTJSMETA:
		sqlcmd:
		maxRows:
		startRow
);

*INLR = *ON;
return;
%>

