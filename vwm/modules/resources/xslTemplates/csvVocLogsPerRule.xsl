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
		<xsl:text>&#xa;</xsl:text>
		<!--title row-->
		<xsl:value-of select="$s"/>		
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="title"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="period"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$d"/>		
			<xsl:value-of select="title2"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:text>&#xa;</xsl:text>
		<xsl:text>&#xa;</xsl:text>

		<xsl:for-each select="equipment">
			<xsl:call-template name="equipmentInfoCompany"/>
			<xsl:call-template name="equipment"/>
		</xsl:for-each>
		
		<!--summary-->
		<!--table header-->
			<xsl:value-of select="$d"/>		
				<xsl:text>Date</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>Equipment</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Coating Single, Composite, Multi-Stage Catalyst/Hardener/Additive Thinner/Reducer/Solvent Batch#</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>VOC of Material</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>VOC of Coating</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Mix Ratio</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Qty Used (gal)</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Coating as Applied</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>Rule Exemption</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Total Lbs VOC</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

		<xsl:text>&#xa;</xsl:text>
		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
			<xsl:value-of select="$d"/>		
				<xsl:text>TOTAL VOC EMISSIONS UNDER RULE </xsl:text><xsl:value-of select="rule"/>
			<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:text>&#xa;</xsl:text>
		
		<xsl:for-each select="summary/summaryTotalEquipment">
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="@equipment"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$s"/>			
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="@qty"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="@voc3"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:value-of select="@totalVoc"/>
			<xsl:value-of select="$d"/>

			<xsl:text>&#xa;</xsl:text>
			
		</xsl:for-each>
		
		<!--SUM-->
		<xsl:value-of select="$s"/>			
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="rule"/><xsl:text> VOC TOTALS</xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="summary/summarySum/@qty"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="summary/summarySum/@voc3"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="summary/summarySum/@totalVoc"/>
		<xsl:value-of select="$d"/>		
		
		<xsl:text>&#xa;</xsl:text>

	</xsl:template>

	<xsl:template name="equipment">	
		<!--table header-->
			<xsl:value-of select="$d"/>		
				<xsl:text>Date</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>Supplier</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Product No</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
	
			<xsl:value-of select="$d"/>		
				<xsl:text>Coating Single, Composite, Multi-Stage Catalyst/Hardener/Additive Thinner/Reducer/Solvent Batch#</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>VOC of Material</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>VOC of Coating</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Mix Ratio</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Qty Used (gal)</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Coating as Applied</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$d"/>		
				<xsl:text>Rule Exemption</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:text>Total Lbs VOC</xsl:text>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

		<xsl:text>&#xa;</xsl:text>
			
		<xsl:for-each select="date">			
			<xsl:for-each select="product">	
				<xsl:value-of select="$d"/>		
					<xsl:value-of select="../@day"/>
				<xsl:value-of select="$d"/>
				<xsl:value-of select="$s"/>

				<xsl:call-template name="rowProductData"/>			
			</xsl:for-each>
			
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalOnProject"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
		 	<xsl:value-of select="$s"/> 

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalOnProjectQty"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>			

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalOnProjectVoc3"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalOnProjectTotalVoc"/>
			<xsl:value-of select="$d"/>

			<xsl:text>&#xa;</xsl:text>
			
			
			
			
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalLabel"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalQty"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>					

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalVoc3"/>
			<xsl:value-of select="$d"/>
			<xsl:value-of select="$s"/>
			
			<xsl:value-of select="$s"/>

			<xsl:value-of select="$d"/>		
				<xsl:value-of select="totalTotalVoc"/>
			<xsl:value-of select="$d"/>

			<xsl:text>&#xa;</xsl:text>
		</xsl:for-each>
		
		<!-- total by equipment-->
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:text>Total for </xsl:text><xsl:value-of select="@name"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="summaryEquipment/summaryEquipmentQty"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="summaryEquipment/summaryEquipmentVoc3"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
			
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:value-of select="summaryEquipment/summaryEquipmentTotalVoc"/>
		<xsl:value-of select="$d"/>

		<xsl:text>&#xa;</xsl:text>
		
		
		
		<xsl:text>&#xa;</xsl:text>
		<xsl:text>&#xa;</xsl:text>
	</xsl:template>


	<xsl:template name="equipmentInfoCompany">
		<xsl:apply-templates select="../company/companyName"/>
		<xsl:apply-templates select="../facility/facilityName"/>

		<xsl:value-of select="$d"/>
				<xsl:text>Equip: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="@name"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:apply-templates select="../company/companyAddress"/>
		<xsl:apply-templates select="../facility/facilityAddress"/>

		<xsl:value-of select="$d"/>
				<xsl:text>Permit No: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="@permitNo"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:apply-templates select="../company/companyCity"/>
		<xsl:apply-templates select="../facility/facilityCity"/>

		<xsl:value-of select="$d"/>
				<xsl:text>Facility ID: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="@facilityID"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:apply-templates select="../company/companyCounty"/>
		<xsl:apply-templates select="../facility/facilityCounty"/>

		<xsl:value-of select="$d"/>
				<xsl:text>Rule No: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="../rule"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:apply-templates select="../company/companyPhone"/>
		<xsl:apply-templates select="../facility/facilityPhone"/>

		<xsl:value-of select="$d"/>
				<xsl:text>GCG No: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="../gcg"/>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:apply-templates select="../company/companyFax"/>
		<xsl:apply-templates select="../facility/facilityFax"/>

		<xsl:value-of select="$d"/>
				<xsl:text>Notes: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:text>&#xa;</xsl:text>

		<xsl:text>&#xa;</xsl:text>

	</xsl:template>


	<xsl:template match="companyName">

		<xsl:value-of select="$d"/>
			<xsl:text>Company Name: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="facilityName">

		<xsl:value-of select="$d"/>
			<xsl:text>Facility Name: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		
	</xsl:template>


	<xsl:template match="companyAddress">

		<xsl:value-of select="$d"/>
			<xsl:text>Address: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="facilityAddress">
		
		<xsl:value-of select="$d"/>
			<xsl:text>Address: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="companyCity">

		<xsl:value-of select="$d"/>
			<xsl:text>City, State, Zip: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="facilityCity">

		<xsl:value-of select="$d"/>
			<xsl:text>City, State, Zip: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="companyCounty">

		<xsl:value-of select="$d"/>
			<xsl:text>County: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="facilityCounty">

		<xsl:value-of select="$d"/>
			<xsl:text>County: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="companyPhone">

		<xsl:value-of select="$d"/>
			<xsl:text>Phone: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="facilityPhone">

		<xsl:value-of select="$d"/>
			<xsl:text>Phone: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="companyFax">

		<xsl:value-of select="$d"/>
			<xsl:text>Fax: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template match="facilityFax">

		<xsl:value-of select="$d"/>
			<xsl:text>Fax: </xsl:text>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="."/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>
		<xsl:value-of select="$s"/>

	</xsl:template>


	<xsl:template name="rowProductData">
	
		<xsl:value-of select="$d"/>		
			<xsl:choose>
				<xsl:when test="supplier = 'N/A'">
        			</xsl:when>
				<xsl:otherwise>
			        	<xsl:value-of select="supplier"/>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>		
			<xsl:choose>
				<xsl:when test="productNo = 'N/A'">
        			</xsl:when>
				<xsl:otherwise>
			        	<xsl:value-of select="productNo"/>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="coatingSingle"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:value-of select="vocOfMaterial"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:value-of select="voc2"/>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:choose>
				<xsl:when test="mixRatio = 'N/A'">
        			</xsl:when>
				<xsl:otherwise>
		        		<xsl:value-of select="mixRatio"/>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:choose>
				<xsl:when test="qtyUsed = 'N/A'">
	        		</xsl:when>
				<xsl:otherwise>
			        	<xsl:value-of select="qtyUsed"/>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:choose>
				<xsl:when test="voc3 = 'N/A'">
        			</xsl:when>
				<xsl:otherwise>
		        		<xsl:value-of select="voc3"/>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>
		
		<xsl:value-of select="$d"/>
			<xsl:choose>
				<xsl:when test="ruleExemption = 'N/A'">
        			</xsl:when>
				<xsl:otherwise>
		        		<xsl:value-of select="RuleExemption"/>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:value-of select="$d"/>
		<xsl:value-of select="$s"/>

		<xsl:value-of select="$d"/>
			<xsl:choose>
				<xsl:when test="totalVoc = 'N/A'">
	        		</xsl:when>
				<xsl:otherwise>
			        	<xsl:value-of select="totalVoc"/>
				</xsl:otherwise>
			</xsl:choose>
		<xsl:value-of select="$d"/>

		<xsl:text>&#xa;</xsl:text>
	</xsl:template>	

</xsl:stylesheet>
