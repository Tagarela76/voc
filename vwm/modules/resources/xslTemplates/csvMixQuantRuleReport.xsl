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
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		<!--title row-->
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>		
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="title"/>
			<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>
			
		<xsl:apply-templates select="company"/>
		<xsl:text>&#xa;</xsl:text>
		<xsl:apply-templates select="facility"/>
		<xsl:text>&#xa;</xsl:text>
		<xsl:apply-templates select="department"/>
		<xsl:text>&#xa;</xsl:text>
		<xsl:text>&#xa;</xsl:text>
		<xsl:apply-templates select="products"/>

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


	<xsl:template match="products">

		<!--table header-->
			<xsl:value-of select="$d"/>		
				<xsl:text>Supplier</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Product code</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Product name</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Rule No</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Grand total of product used (US gal)</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Product qtys not used (US gal)</xsl:text>
			<xsl:value-of select="$d"/>			

		<xsl:text>&#xa;</xsl:text>

		<xsl:for-each select="product">		

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="supplier"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="productCode"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="productName"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:apply-templates select="rules"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="used"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="notUsed"/>
			<xsl:value-of select="$d"/>
			
			<xsl:text>&#xa;</xsl:text>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="rules">

		<xsl:value-of select="$d"/>
		<xsl:for-each select="rule">
			<!--<xsl:text>Rule </xsl:text>
			<xsl:value-of select="name"/>: <xsl:text/>
			<xsl:value-of select="qtyRule"/>
			<xsl:text>
			</xsl:text>-->
			
			<xsl:value-of select="name"/>
			<xsl:text>
			</xsl:text> 
		</xsl:for-each>		
		<xsl:value-of select="$d"/>
	</xsl:template>
	
</xsl:stylesheet>
