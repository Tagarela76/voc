<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>
	
	<xsl:template match="page">
		<h1><center> <xsl:value-of select="title"/> </center></h1>
		<!--<h3><center> <xsl:value-of select="book"/> </center></h3>-->
		<h3><center> <xsl:value-of select="period"/> </center></h3>
		
		<xsl:apply-templates select="company"/>
		<xsl:apply-templates select="facility"/>
		<xsl:apply-templates select="department"/>
				
		<hr class="none"/>
		
		<!-- Table  template-->
		<xsl:call-template name="table"/>
	</xsl:template>
	
	<xsl:template name="table">
		<table border="1">
			<!-- Table header -->
			<tr bgcolor="gray">
				<th>  </th>
				<th> CRC Product Code </th>
				<th> Color </th>
				<th> MATERIAL VOC (lb/gal) </th>
				<th> COATING VOC (lb/gal) </th>
			</tr>
			
			<xsl:for-each select="mpsGroup">
				<tr>
					<!-- <EMPTY HEADER> -->
					<td>
						<xsl:for-each select="product">
							<div> <b> <xsl:value-of select="ID"/> </b> </div>
						</xsl:for-each>
					</td>
					
					<!-- CRC Product Code -->
					<td>
						<xsl:for-each select="product">
							<div> <xsl:value-of select="productCode"/> </div>
						</xsl:for-each>
					</td>
					
					<!-- Color -->
					<td>
						<xsl:for-each select="product">
							<div> <xsl:value-of select="color"/> </div>
						</xsl:for-each>
					</td>
					
					<!-- MATERIAL VOC -->
					<td>
						<xsl:for-each select="product">
							<div> <xsl:value-of select="materialVoc"/> </div>
						</xsl:for-each>
					</td>
					
					<!-- COATING VOC -->
					<td>
						<table>
							<xsl:for-each select="product">
								<tr><td><div style="height:20px;">
									<xsl:choose>
										<xsl:when test="coatingVoc = 'N/A'">
										</xsl:when>
										
										<xsl:otherwise>
											<xsl:value-of select="coatingVoc"/>
										</xsl:otherwise>
									</xsl:choose>
								</div></td></tr>	
							</xsl:for-each>
						</table>
					</td>
				</tr>
			</xsl:for-each>
		</table>
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
	
</xsl:stylesheet>