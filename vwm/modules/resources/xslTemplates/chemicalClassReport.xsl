<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>

	<xsl:template match="page">
		<html>
			<body>
				<h1><center><xsl:value-of select="title"/></center></h1>

				<xsl:apply-templates select="company"/>
				<xsl:apply-templates select="facility"/>
				<xsl:apply-templates select="department"/>
				
				<hr class="none"/>
	
				<xsl:apply-templates select="items"/>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template match="company">
		<h4>COMPANY NAME: <xsl:value-of select="companyName"/></h4>
		<h4>COMPANY ADDRESS: <xsl:value-of select="companyAddress"/></h4>
	</xsl:template>
	
	<xsl:template match="facility">
		<h4>FACILITY NAME: <xsl:value-of select="facilityName"/></h4>
		<h4>FACILITY ADDRESS: <xsl:value-of select="facilityAddress"/></h4>
	</xsl:template>
	
	<xsl:template match="department">
		<h4>DEPARTMENT NAME: <xsl:value-of select="departmentName"/></h4>
	</xsl:template>
	
	<xsl:template match="items">
		<xsl:apply-templates select="hazardClass"/>
	</xsl:template>

	<xsl:template match="hazardClass">
		<h4>HAZARD CLASS <xsl:value-of select="@class"/></h4>

		<table border="1">
			<tr bgcolor="gray">
				<th>COMMON NAME</th>
        		<th>CHEMICAL NAME</th>
        		<th>AMOUNT STORED</th>
        		<th>O.S. USE</th>
        		<th>C.S. USE</th>
        		<th>LOCATION OF STORAGE</th>
        		<th>LOCATION OF USE</th>
      		</tr>

			<xsl:for-each select="item">
				<tr>
	 				<td><xsl:value-of select="commonName"/></td>
	 				<td><xsl:value-of select="chemicalName"/></td>
	 				<td><xsl:value-of select="amount"/></td>
	 				<td><xsl:value-of select="osUse"/></td>
	 				<td><xsl:value-of select="csUse"/></td>
	 				
	 				<td>
	 					<div style="height:20px;">
		 					<xsl:choose>
		 						<xsl:when test="locationOfStorage = 'N/A'">
		 						</xsl:when>
		 						
		 						<xsl:otherwise>
		 							<xsl:value-of select="locationOfStorage"/>
		 						</xsl:otherwise>
		 					</xsl:choose>
		 				</div>
	 				</td>
	 				
	 				<td>
	 					<div style="height:20px;">
		 					<xsl:choose>
		 						<xsl:when test="locationOfUse = 'N/A'">
		 						</xsl:when>
		 						
		 						<xsl:otherwise>
		 							<xsl:value-of select="locationOfUse"/>
		 						</xsl:otherwise>
		 					</xsl:choose>
		 				</div>
	 				</td>
				</tr>	
			</xsl:for-each>
			<xsl:apply-templates select="total"/>			
		</table>			
	</xsl:template>
	
	<xsl:template match="total">
		<tr bgcolor="gray">	
			<td colspan="7"><b>Interior Storage: <xsl:value-of select="@IS"/>, Exterior Storage: <xsl:value-of select="@ES"/>, Open System Use: <xsl:value-of select="@OS"/>, Closed System Use: <xsl:value-of select="@CS"/></b></td>
		</tr>
	</xsl:template>

</xsl:stylesheet>