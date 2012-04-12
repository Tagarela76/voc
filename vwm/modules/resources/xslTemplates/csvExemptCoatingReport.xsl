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
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="title"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--rule row-->
		<xsl:value-of select="$s"/>
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
		<xsl:text>&#xa;</xsl:text>
		
		<!--section row-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>		
			<xsl:text>Exempt Coatings Under Section:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="section"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--categories row-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>		
			<xsl:text>Categories:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="categories"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!--Empry row-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>		
		<xsl:text>&#xa;</xsl:text>
		
		<xsl:for-each select="facility/equipment">
			<!--facility name row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$d"/>		
				<xsl:text>Facility Name:</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="../name"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$s"/>
			<xsl:text>&#xa;</xsl:text>
			
			<!--location row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$d"/>		
				<xsl:text>Location Address:</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="../location"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$s"/>
			<xsl:text>&#xa;</xsl:text>
			
			<!--Contact row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$d"/>		
				<xsl:text>Contact Name:</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="../contactName"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$s"/>
			<xsl:text>&#xa;</xsl:text>
			
			<!--Tel row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$d"/>		
				<xsl:text>Tel No.:</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="../TelNo"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$s"/>
			<xsl:text>&#xa;</xsl:text>
			
			<!--facility id row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$d"/>		
				<xsl:text>Facility ID No.:</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="@id"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$s"/>
			<xsl:text>&#xa;</xsl:text>
			
			<!--permit row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$d"/>		
				<xsl:text>SCAQMD Permit No.:</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="@permit"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$s"/>
			<xsl:text>&#xa;</xsl:text>
			
			<!--Empry row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>		
			<xsl:text>&#xa;</xsl:text>
			
			<xsl:call-template name="equipmentData"/>
			
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
			
			<!--Empry row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>		
			<xsl:text>&#xa;</xsl:text>
			
		</xsl:for-each>
		
		<!--prepared row-->
		<xsl:value-of select="$d"/>
			<xsl:text>Prepared By</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>		
		<xsl:text>&#xa;</xsl:text>
		
		<!--print name row-->		
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:text>Print Name:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="printName"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$s"/>		
		<xsl:text>&#xa;</xsl:text>
		
		<!--tel no row-->		
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:text>Tel No.:</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="usersTelNo"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$s"/>		
		<xsl:text>&#xa;</xsl:text>
		
		<!--date row-->		
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:text>Date</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="date"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$s"/>		
		<xsl:text>&#xa;</xsl:text>
		
			
	</xsl:template>
	
	<xsl:template name="equipmentData">
		<!-- Table Header -->
		<xsl:value-of select="$d"/>		
			<xsl:text>PRODUCT ID</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>CATEGORY</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
				
		<xsl:value-of select="$d"/>		
			<xsl:text>AMOUNT USED (GAL)</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:text>VOC OF COATING</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>EXEMPT UNDER SECTION</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>
		
		<!-- Table data -->
		<xsl:for-each select="products/product">
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="productID"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="category"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
				
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="amount"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
		
			<xsl:value-of select="$d"/>
				<xsl:value-of select="vocOfCoating"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="exempt"/>
			<xsl:value-of select="$d"/>
			<xsl:text>&#xa;</xsl:text>
		</xsl:for-each>
				
		<!-- TOTALS -->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>		
			<xsl:text>TOTALS</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
				
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="totalAmount"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="totalVoc"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="totalExempt"/>
		<xsl:value-of select="$d"/>		
		
	</xsl:template>
	
</xsl:stylesheet>

		