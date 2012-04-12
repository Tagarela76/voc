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
		<xsl:apply-templates select="equipments"/>

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


	<xsl:template match="equipments">
		<!--table header-->
			<xsl:value-of select="$d"/>		
				<xsl:text>Equipment</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Name</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>CAS</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>VOC/PM</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Low</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>High</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Avg</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Total</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Avg/Hour</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>CA-AB2588</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>SARA313</xsl:text>
			<xsl:value-of select="$d"/>

		<xsl:text>&#xa;</xsl:text>

		<!--table body-->
		<xsl:for-each select="equipment">
			<xsl:for-each select="compound">

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="../@description"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="compoundName"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="cas"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="vocpm"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="low"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="high"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="avg"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="total"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:value-of select="avgHour"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:choose>
						<xsl:when test="caab2588 = 'N/A'">
	        				</xsl:when>
						<xsl:otherwise>
		          				<xsl:value-of select="caab2588"/>
				        	</xsl:otherwise>
				        </xsl:choose>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:value-of select="$d"/>		
					<xsl:choose>
						<xsl:when test="sara313 = 'N/A'">
        					</xsl:when>
						<xsl:otherwise>
		          				<xsl:value-of select="sara313"/>
				        	</xsl:otherwise>
				        </xsl:choose>      		
				<xsl:value-of select="$d"/>
				<xsl:text>&#xa;</xsl:text>
			</xsl:for-each>
		</xsl:for-each>
	</xsl:template>
		
</xsl:stylesheet>
