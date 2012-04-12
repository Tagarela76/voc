<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>

	<xsl:template match="page">
		<html>
			<body>
				<h2><xsl:value-of select="title"/></h2>				
				<hr class="none"/>
				
				<table>
						<tr>
							<td>Rule:</td>
							<td><xsl:value-of select="rule"/></td>
							<td>Month/Year:</td>
							<td><xsl:value-of select="monthYear"/></td>
						</tr>
						<tr>
							<td>Exempt Coatings Under Section:</td>
							<td><xsl:value-of select="section"/></td>
							<td></td><td></td>
						</tr>
						<tr>
							<td>Categories:</td>
							<td><xsl:value-of select="categories"/></td>
							<td></td><td></td>
						</tr>				
				</table>
				<hr class="none"/>
				
				<xsl:for-each select="facility/equipment">
					<table>
						<tr>
							<td>Facility Name:</td>
							<td><xsl:value-of select="../name"/></td>
						</tr>										
						<tr>
							<td>Location Address:</td>
							<td><xsl:value-of select="../location"/></td>
						</tr>
						<tr>
							<td>Contact Name:</td>
							<td><xsl:value-of select="../contactName"/></td>
						</tr>																						
						<tr>
							<td>Tel No.:</td>
							<td><xsl:value-of select="../TelNo"/></td>
						</tr>								
						<tr>
							<td>Facility ID No.:</td>
							<td><xsl:value-of select="@id"/></td>
						</tr>
						<tr>
							<td>SCAQMD Permit No.:</td>
							<td><xsl:value-of select="@permit"/></td>
						</tr>																																
					</table>
					<hr class="none"/>
					<br class="none"/>
					
					<xsl:call-template name="equipmentData"/>
					
					<br class="none"/>
					<br class="none"/>
					<br class="none"/>
					
				</xsl:for-each>
				Prepared By:
				<table>
					<tr>
						<td>Print Name:</td>
						<td><xsl:value-of select="printName"/></td>
					</tr>
					<tr>
						<td>Tel No.:</td>
						<td><xsl:value-of select="usersTelNo"/></td>
					</tr>
					<tr>
						<td>Date:</td>
						<td><xsl:value-of select="date"/></td>
					</tr>
				</table>
			</body>
		</html>
	</xsl:template>
	
	
	<xsl:template name="equipmentData">
		<table border="1">
		
			<!-- Table Header -->
			<tr bgcolor="gray">
				<th> PRODUCT ID </th>
				<th> CATEGORY </th>
				<th> AMOUNT USED (GAL)  </th>
				<th> VOC OF COATING </th>
				<th> EXEMPT UNDER SECTION </th>				
			</tr>
			
			<!-- Table Data-->
			<xsl:for-each select="products/product">
				<tr>
					<td> <xsl:value-of select="productID"/> </td>
					<td> <xsl:value-of select="category"/> </td>
					<td> <xsl:value-of select="amount"/> </td>
					<td> <xsl:value-of select="vocOfCoating"/> </td>
					<td> <xsl:value-of select="exempt"/> </td>
				</tr>
			</xsl:for-each>
			<!--TOTALS-->
				<tr>
					<td colspan="2"> TOTALS </td>					
					<td> <xsl:value-of select="totalAmount"/> </td>
					<td> <xsl:value-of select="totalVoc"/> </td>
					<td> <xsl:value-of select="totalExempt"/> </td>
				</tr>
		</table>
	</xsl:template>	
	
</xsl:stylesheet>

		