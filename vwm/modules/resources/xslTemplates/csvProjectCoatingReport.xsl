<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="text"/>
	<xsl:strip-space elements ="*"/>
	<xsl:param name="s"></xsl:param>
	<xsl:param name="d"></xsl:param>

	<xsl:template match="page">
		<!--Empry row need for xls generation-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>			
		<xsl:text>&#xa;</xsl:text>
		
		<!--title row-->
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="title"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>		
		<xsl:text>&#xa;</xsl:text>
		
		<!--rule row-->
		<xsl:value-of select="$d"/>		
			<xsl:text>Rule:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="rule"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>
			<xsl:text>Month/Year</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="monthYear"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>				
		
		<!--categories row-->
		<xsl:value-of select="$d"/>		
			<xsl:text>Categories:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="categories"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--client name row-->
		<xsl:value-of select="$d"/>		
			<xsl:text>Client Name:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="clientName"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--client spec row-->		
		<xsl:value-of select="$d"/>		
			<xsl:text>Client Specification:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="clientSpecification"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--Empry row-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!-- table header-->
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>Name#1</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>Name#2</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>Name#3</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		
		<!-- table body -->		
		<xsl:value-of select="$d"/>		
			<xsl:text>Name#3</xsl:text>
		<xsl:value-of select="$d"/>
				
		<!--supplier name row-->		
		<xsl:value-of select="$d"/>		
			<xsl:text>Name of Coating Manufacurer Contacted:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>						
		
		<xsl:for-each select="table/supplierName/name">
			<xsl:value-of select="$d"/>
				<xsl:value-of select="."/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
		</xsl:for-each>
		<xsl:text>&#xa;</xsl:text>
		
		<!--contact person row-->		
		<xsl:value-of select="$d"/>		
			<xsl:text>Contact Person:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>						
		
		<xsl:for-each select="table/supplierContact/contact">
			<xsl:value-of select="$d"/>
				<xsl:value-of select="."/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
		</xsl:for-each>
		<xsl:text>&#xa;</xsl:text>
		
		<!--contact phone row-->		
		<xsl:value-of select="$d"/>		
			<xsl:text>Telephone:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>						
		
		<xsl:for-each select="table/supplierPhone/phone">
			<xsl:value-of select="$d"/>
				<xsl:value-of select="."/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
		</xsl:for-each>
		<xsl:text>&#xa;</xsl:text>		
		
		<!--reason row-->		
		<xsl:value-of select="$d"/>		
			<xsl:text>Telephone:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>						
		
		<xsl:for-each select="table/supplierReason/reason">
			<xsl:value-of select="$d"/>
				<xsl:value-of select="."/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
		</xsl:for-each>
		<xsl:text>&#xa;</xsl:text>
		
		<!--Empry row-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!-- Summary -->
		<xsl:value-of select="$d"/>		
			<xsl:text>Summary for compliant problem/failure:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="summary"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--Empry row-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--Empry row-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--footer -->
		<xsl:value-of select="$d"/>		
			<xsl:text>Report by:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>Signature:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>Date:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
												

	</xsl:template>
	
</xsl:stylesheet>

		