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

		<xsl:apply-templates select="items"/>

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


	<xsl:template match="items">
		<xsl:apply-templates select="hazardClass"/>
	</xsl:template>


	<xsl:template match="hazardClass">

		<xsl:value-of select="$d"/>		
			<xsl:text>HAZARD CLASS: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="@class"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:text>&#xa;</xsl:text>

		<!--table header-->
			<xsl:value-of select="$d"/>		
				<xsl:text>Common name</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Chemical name</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Amount stored</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>O.S. use</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>C.S. use</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Location of storage</xsl:text>
			<xsl:value-of select="$d"/>			
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Location of use</xsl:text>
			<xsl:value-of select="$d"/>			

		<xsl:text>&#xa;</xsl:text>

		<xsl:for-each select="item">

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="commonName"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="chemicalName"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="amount"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="osUse"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="csUse"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:choose>
					<xsl:when test="locationOfStorage = 'N/A'">
        				</xsl:when>
					<xsl:otherwise>
				        	<xsl:value-of select="locationOfStorage"/>
					</xsl:otherwise>
				</xsl:choose>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:choose>
					<xsl:when test="locationOfUse = 'N/A'">
	        			</xsl:when>
					<xsl:otherwise>
			          		<xsl:value-of select="locationOfUse"/>
					</xsl:otherwise>
				</xsl:choose>      	
			<xsl:value-of select="$d"/>

			<xsl:text>&#xa;</xsl:text>
		</xsl:for-each>
		<xsl:apply-templates select="total"/>
		<xsl:text>&#xa;</xsl:text>
		<xsl:text>&#xa;</xsl:text>
	</xsl:template>
	
	<xsl:template match="total">
	<xsl:value-of select="$d"/>	
		Interior Storage: <xsl:value-of select="@IS"/>, Exterior Storage: <xsl:value-of select="@ES"/>, Open System Use: <xsl:value-of select="@OS"/>, Closed System Use: <xsl:value-of select="@CS"/>
	<xsl:value-of select="$d"/>
	<xsl:text>&#xa;</xsl:text>	
	</xsl:template>
	
</xsl:stylesheet>
