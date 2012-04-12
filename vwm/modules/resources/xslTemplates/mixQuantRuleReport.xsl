<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>
	
	<xsl:template match="page">
		<center><h1> <xsl:value-of select="title"/> </h1></center>
		
		<xsl:apply-templates select="company"/>
		<xsl:apply-templates select="facility"/>
		<xsl:apply-templates select="department"/>
		
		
		<xsl:apply-templates select="products"/>
		
	</xsl:template>
	
	<xsl:template match="company">
		<h3>Company Name: <xsl:value-of select="companyName"/> </h3>
		<h3>Company Address:  <xsl:value-of select="companyAddress"/> </h3>
		<hr class="none"/>
	</xsl:template>
	
	<xsl:template match="facility">
		<h3>Facility Name: <xsl:value-of select="facilityName"/> </h3>
		<h3>Facility Address: <xsl:value-of select="facilityAddress"/> </h3>
		<hr class="none"/>
	</xsl:template>
	
	<xsl:template match="department">
		<h3>Department Name: <xsl:value-of select="departmentName"/> </h3>
		<hr class="none"/>
	</xsl:template>
	
	
	<xsl:template match="products">
		<table border="1">
			<tr bgcolor="gray">
				<th> Supplier </th>
				<th> Product Code </th>
				<th> Product Name </th>
				<th> Rule No</th>
				<th> Used </th>
				<th> Not Used </th>
			</tr>
			
			<xsl:for-each select="product">
				<tr>
					<td> <xsl:value-of select="supplier"/> </td>
					<td> <xsl:value-of select="productCode"/> </td>
					<td> <xsl:value-of select="productName"/> </td>
					
					<td>
						<table>
							<xsl:for-each select="rules/rule">
							<tr><td>
								<!--<b> Rule <xsl:value-of select="name"/>:</b> <xsl:value-of select="qtyRule"/>-->
								<xsl:value-of select="name"/>
							</td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					<td> <xsl:value-of select="used"/> </td>
					<td> <xsl:value-of select="notUsed"/> </td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
</xsl:stylesheet>