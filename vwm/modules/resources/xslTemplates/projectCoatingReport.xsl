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
							<td>Categories:</td>
							<td><xsl:value-of select="categories"/></td>
							<td></td><td></td>
						</tr>				
				</table>
				<hr class="none"/>
				
				
				<table>
					<tr>
						<td>Client Name:</td>
						<td><xsl:value-of select="clientName"/></td>
					</tr>										
					<tr>
						<td>Client Specification:</td>
						<td><xsl:value-of select="clientSpecification"/></td>
					</tr>																																				
				</table>
				<hr class="none"/>
				<br class="none"/>
				
				<table border="1">
					<tr bgcolor="gray">
						<th></th>						
						<th>Name#1</th>
						<th>Name#2</th>
						<th>Name#3</th>
					</tr>										
					<tr>
						<td>Name of Coating Manufacturer Contacted:</td>
						<xsl:for-each select="table/supplierName/name">
							<td><xsl:value-of select="."/></td>
						</xsl:for-each>						
					</tr>
					<tr>
						<td>Contact Person:</td>
						<xsl:for-each select="table/supplierContact/contact">
							<td><xsl:value-of select="."/></td>
						</xsl:for-each>						
					</tr>
					<tr>
						<td>Telephone:</td>
						<xsl:for-each select="table/supplierPhone/phone">
							<td><xsl:value-of select="."/></td>
						</xsl:for-each>						
					</tr>
					<tr>
						<td>Reason for Non-availability:</td>
						<xsl:for-each select="table/supplierReason/reason">
							<td><xsl:value-of select="."/></td>
						</xsl:for-each>						
					</tr>																																									
				</table>													
				<hr class="none"/>
				Summary for compliant coating problem/failure
				<br class="none"/>
				<xsl:value-of select="summary"/>
				<br class="none"/>
				<br class="none"/>
				<hr class="none"/>
									
				<table>
					<tr>
						<td>Report by:</td>						
					</tr>
					<tr>
						<td>Signature:</td>						
					</tr>
					<tr>
						<td>Date:</td>						
					</tr>
				</table>
			</body>
		</html>
	</xsl:template>
		
	
</xsl:stylesheet>

		