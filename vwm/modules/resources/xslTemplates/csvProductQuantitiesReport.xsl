<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="text"/>
	<xsl:strip-space elements ="*"/>
	<xsl:param name="s"></xsl:param>
	<xsl:param name="d"></xsl:param>

	<xsl:template match="/">
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
				<xsl:value-of select="page/title"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		<!--book row-->
		<!--	<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>	
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="page/book"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>-->
		
		<!--period row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>	
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="page/period"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<xsl:apply-templates select="page/company"/>
		<xsl:text>&#xa;</xsl:text>
		<xsl:apply-templates select="page/facility"/>
		<xsl:text>&#xa;</xsl:text>
		<xsl:apply-templates select="page/department"/>
		<xsl:text>&#xa;</xsl:text>
		<xsl:text>&#xa;</xsl:text>
		
		<!--empty row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>	
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		<!--table header-->			
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>CRC Product Code</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>Color</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>MATERIAL VOC (lb/gal)</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>COATING VOC (lb/gal)</xsl:text>
			<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<!--table body-->
		<xsl:for-each select="page/mpsGroup/product">
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="ID"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="productCode"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="color"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="materialVoc"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>
				<xsl:choose>
					<xsl:when test="coatingVoc = 'N/A'">
        				</xsl:when>
					<xsl:otherwise>
		          			<xsl:value-of select="coatingVoc"/>
				        </xsl:otherwise>
			        </xsl:choose>
			<xsl:value-of select="$d"/>

			<xsl:text>&#xa;</xsl:text>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="company">
		<xsl:value-of select="$d"/>		
			<xsl:text>COMPANY NAME: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="companyName"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>
		
		<xsl:value-of select="$d"/>		
			<xsl:text>COMPANY ADDRESS: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="companyAddress"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>
	</xsl:template>

	
	<xsl:template match="facility">
		<xsl:value-of select="$d"/>		
			<xsl:text>FACILITY NAME: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="facilityName"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:value-of select="$d"/>		
			<xsl:text>FACILITY ADDRESS: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="facilityAddress"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>
	</xsl:template>


	<xsl:template match="department">
		<xsl:value-of select="$d"/>		
			<xsl:text>DEPARTMENT NAME: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="departmentName"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>
	</xsl:template>

</xsl:stylesheet>
