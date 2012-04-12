<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>

	<xsl:template match="page">
		<html>
			<body>
				<h2><xsl:value-of select="title"/></h2>
				<h2><xsl:value-of select="period"/></h2>
				<h2><xsl:value-of select="title2"/></h2>
				<hr class="none"/>
				
				<xsl:for-each select="equipment">
					<table>
						<tr>
							<td>
								<!-- Left column -->
								<xsl:apply-templates select="../company"/>
								<xsl:apply-templates select="../facility"/>
								<xsl:apply-templates select="../department"/>
							</td>
							
							<td>
								<!-- Right column -->
								<xsl:call-template name="equipmentInfo"/>
							</td>
						</tr>
					</table>
					
					<br class="none"/>
					
					<xsl:call-template name="equipmentData"/>
					<br class="none"/>
					<br class="none"/>
					<br class="none"/>
					
				</xsl:for-each>
				<xsl:call-template name="summary"/>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template name="equipmentInfo">
		<table>
			<tr>
				<td><right><b>	Equip:								</b></right></td>
				<td><left>		<xsl:value-of select="@name"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Permit No:							</b></right></td>
				<td><left>		<xsl:value-of select="@permitNo"/>			</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Facility ID:						</b></right></td>
				<td><left>		<xsl:value-of select="@facilityID"/>	</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Rule No:							</b></right></td>
				<td><left>		<xsl:value-of select="../rule"/>	</left></td>
			</tr>
			
			<tr>
				<td><right><b>	GCG No:								</b></right></td>
				<td><left>		<xsl:value-of select="../gcg"/>			</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Notes:								</b></right></td>
				<td><left>		<xsl:value-of select="t"/>			</left></td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="company">
		<table>
			<tr>
				<td><right><b>	Company Name:								</b></right></td>
				<td><left>		<xsl:value-of select="companyName"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Address:									</b></right></td>
				<td><left>		<xsl:value-of select="companyAddress"/>	</left></td>
			</tr>
			
			<tr>
				<td><right><b>	City, State, Zip:							</b></right></td>
				<td><left>		<xsl:value-of select="companyCity"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	County:										</b></right></td>
				<td><left>		<xsl:value-of select="companyCounty"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Phone:										</b></right></td>
				<td><left>		<xsl:value-of select="companyPhone"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Fax:										</b></right></td>
				<td><left>		<xsl:value-of select="companyFax"/>		</left></td>
			</tr>
		</table>
	</xsl:template>
	
	<xsl:template match="facility">
		<table>
			<tr>
				<td><right><b>	Facility Name:								</b></right></td>
				<td><left>		<xsl:value-of select="facilityName"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Address:									</b></right></td>
				<td><left>		<xsl:value-of select="facilityAddress"/>	</left></td>
			</tr>
			
			<tr>
				<td><right><b>	City, State, Zip:							</b></right></td>
				<td><left>		<xsl:value-of select="facilityCity"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	County:										</b></right></td>
				<td><left>		<xsl:value-of select="facilityCounty"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Phone:										</b></right></td>
				<td><left>		<xsl:value-of select="facilityPhone"/>		</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Fax:										</b></right></td>
				<td><left>		<xsl:value-of select="facilityFax"/>		</left></td>
			</tr>
		</table>
	</xsl:template>
	
	<xsl:template match="department">
		<table>
			<tr>
				<td><right><b>	Department Name:								</b></right></td>
				<td><left>		<xsl:value-of select="departmentName"/>		</left></td>
			</tr>
		</table>
	</xsl:template>
	
	<xsl:template name="equipmentData">
		<table border="1">
		
			<!-- Table Header -->
			<tr bgcolor="gray">
				<th> Date </th>
				<th> Manufacturer </th>
				<th> Product No </th>
				<th> Coating Single, Composite, Multi-Stage Catalyst/Hardener/Additive Thinner/Reducer/Solvent Batch#  </th>
				<th> VOC of Material </th>
				<th> VOC of Coating </th>
				<th> Mix Ratio </th>
				<th> Qty Used (gal) </th>
				<th> Coating as Applied </th>
				<th> Rule Exemption </th>
				<th> Total Lbs VOC </th>
			</tr>
			
			<!-- Table Data-->			
			<xsl:for-each select="date">
				<tr>
					<td> <xsl:value-of select="@day"/>
						
						<xsl:for-each select="product">
							<div style="height:20px;"></div>
						</xsl:for-each>
					
					</td>
					<xsl:call-template name="rowProductData"/>					
				</tr>
			</xsl:for-each>
				<tr bgcolor="gray">
					<xsl:call-template name="equipmentTotal"/>
				</tr>
		</table>
	</xsl:template>
	
	<xsl:template name="rowProductData">
		<!-- Manufacturer -->
		<td>
			<xsl:for-each select="product">
				<div>
					<xsl:choose>
						<xsl:when test="supplier = 'N/A'">
        					</xsl:when>
						<xsl:otherwise>
			        		<xsl:value-of select="supplier"/>
						</xsl:otherwise>
					</xsl:choose> 
				</div>
			</xsl:for-each>
			
			<div style="height:20px;"></div>
			<div style="height:20px;"></div>
		</td>
		
		<!-- Product No. -->
		<td>
			<xsl:for-each select="product">
				<div> 
					<xsl:choose>
						<xsl:when test="productNo = 'N/A'">
        					</xsl:when>
						<xsl:otherwise>
			        		<xsl:value-of select="productNo"/>
						</xsl:otherwise>
					</xsl:choose><!-- <xsl:value-of select="productNo"/> --> 
				</div>
			</xsl:for-each>
			
			<div style="height:20px;"></div>
			<div style="height:20px;"></div>
		</td>
		
		<!-- Coating Single, Composite, Multi-Stage Catalyst/Hardener/Additive Thinner/Reducer/Solvent Batch# -->
		<td>
			<xsl:for-each select="product">
				<div> <xsl:value-of select="coatingSingle"/> </div>
			</xsl:for-each>
			
			<div> <xsl:value-of select="totalOnProject"/> </div>
			<div> <xsl:value-of select="totalLabel"/> </div>
		</td>
		
		<!-- VOC of Material  -->
		<td>
			<xsl:for-each select="product">
				<div> <xsl:value-of select="vocOfMaterial"/> </div>
			</xsl:for-each>
			
			<div style="height:20px;"></div>
			<div style="height:20px;"></div>
		</td>
		
		<!-- VOC of Coating  -->
		<td>
			<xsl:for-each select="product">
				<div> <xsl:value-of select="voc2"/> </div>
			</xsl:for-each>
			
			<div style="height:20px;"></div>
			<div style="height:20px;"></div>
		</td>
		
		<!-- Mix Ratio  -->
		<td>
			<xsl:for-each select="product">
				<div style="height:20px;">
					<xsl:choose>
						<xsl:when test="mixRatio = 'N/A'">
						</xsl:when>
						
						<xsl:otherwise>
							<xsl:value-of select="mixRatio"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:for-each>
			
			<div style="height:20px;"></div>
			<div style="height:20px;"></div>
		</td>
		
		<!-- Qty Used (gal)  -->
		<td>
			<xsl:for-each select="product">
				<div style="height:20px;">
					<xsl:choose>
						<xsl:when test="qtyUsed = 'N/A'">
						</xsl:when>
						
						<xsl:otherwise>
							<xsl:value-of select="qtyUsed"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:for-each>
			
			<div> <xsl:value-of select="totalOnProjectQty"/> </div>
			<div> <xsl:value-of select="totalQty"/> </div>
		</td>
		
		<!-- VOC *3 as Applied  -->
		<td>
			<xsl:for-each select="product">
				<div style="height:20px;">
					<xsl:choose>
						<xsl:when test="voc3 = 'N/A'">
						</xsl:when>
						
						<xsl:otherwise>
							<xsl:value-of select="voc3"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:for-each>
			
			<div> <xsl:value-of select="totalOnProjectVoc3"/> </div>
			<div> <xsl:value-of select="totalVoc3"/> </div>
		</td>
		
		<!-- Rule Exemption  -->
		<td>
			<xsl:for-each select="product">
				<div style="height:20px;">
					<xsl:choose>
						<xsl:when test="ruleExemption = 'N/A'">
						</xsl:when>
						
						<xsl:otherwise>
							<xsl:value-of select="ruleExemption"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:for-each>
			
			<div style="height:20px;"></div>
			<div style="height:20px;"></div>
		</td>
		
		<!-- Total Lbs VOC  -->
		<td>
			<xsl:for-each select="product">
				<div style="height:20px;">
					<xsl:choose>
						<xsl:when test="totalVoc = 'N/A'">
						</xsl:when>
						
						<xsl:otherwise>
							<xsl:value-of select="totalVoc"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:for-each>
			
			<div> <xsl:value-of select="totalOnProjectTotalVoc"/> </div>
			<div> <xsl:value-of select="totalTotalVoc"/> </div>
						
		</td>
	</xsl:template>
	
	<xsl:template match="product">
		<td> <xsl:value-of select="productNo"/> </td>
		<td> <xsl:value-of select="coatingSingle"/> </td>
		<td>  </td>
		<td> <xsl:value-of select="voc2"/> </td>
		<td>  </td>
		<td> <xsl:value-of select="qtyUsed"/> </td>
		<td>  </td>
		<td>  </td>
		<td>  </td>
	</xsl:template>
	
	<xsl:template name="equipmentTotal">		
		<td> <div style="height:20px;"></div> </td>
		<td> <div style="height:20px;"></div> </td>
		<td> <div style="height:20px;"></div> </td>
		<td> <div style="height:20px;"> Total for <xsl:value-of select="./@name"/></div></td>
		<td> <div style="height:20px;"></div></td>
		<td> <div style="height:20px;"></div> </td>
		<td> <div style="height:20px;"></div> </td>
		<td> <div style="height:20px;"></div> </td>
		<td> <div style="height:20px;"><xsl:value-of select="summaryEquipment/summaryEquipmentQty"/> </div></td>
		<td> <div style="height:20px;"><xsl:value-of select="summaryEquipment/summaryEquipmentVoc3"/></div></td>
		<td> <div style="height:20px;"><xsl:value-of select="summaryEquipment/summaryEquipmentTotalVoc"/> </div></td>		
	</xsl:template>
	
	<xsl:template name="summary">
		<table>
			<tr>
				<td>
					<!-- Left column -->
					<xsl:apply-templates select="company"/>
					<xsl:apply-templates select="facility"/>
					<xsl:apply-templates select="department"/>
				</td>
							
				<td>
					<!-- Right column -->
					<xsl:call-template name="equipmentInfoSummary"/>
				</td>
			</tr>
		</table>
		
		<br class="none"/>
					
		<xsl:call-template name="summaryData"/>
	</xsl:template>
	
	<xsl:template name="equipmentInfoSummary">
		<table>
			<tr>
				<td><right></right></td>
				<td><left></left></td>
			</tr>
			
			<tr>
				<td><right></right></td>
				<td><left></left></td>
			</tr>
			
			<tr>
				<td><right></right></td>
				<td><left></left></td>
			</tr>
			
			<tr>
				<td><right><b>	Rule No:							</b></right></td>
				<td><left>		<xsl:value-of select="rule"/>	</left></td>
			</tr>
			
			<tr>
				<td><right><b>	GCG No:								</b></right></td>
				<td><left>		<xsl:value-of select="gcg"/>			</left></td>
			</tr>
			
			<tr>
				<td><right><b>	Notes:								</b></right></td>
				<td><left>		<xsl:value-of select="t"/>			</left></td>
			</tr>
		</table>
	</xsl:template>
	
	<xsl:template name="summaryData">
		<table border="1">
		
			<!-- Table Header -->
			<tr bgcolor="gray">
				<th> Date </th>
				<th> Equipment </th>
				<th> Coating Single, Composite, Multi-Stage Catalyst/Hardener/Additive Thinner/Reducer/Solvent Batch#  </th>
				<th> VOC of Material </th>
				<th> VOC of Coating </th>
				<th> Mix Ratio </th>
				<th> Qty Used (gal) </th>
				<th> Coating as Applied </th>
				<th> Rule Exemption </th>
				<th> Total Lbs VOC </th>
			</tr>
			<tr>
				<th colspan="10">TOTAL VOC EMISSIONS UNDER RULE <xsl:value-of select="rule"/></th>				
			</tr>
			
			<!-- Table Data-->			
			<xsl:for-each select="summary/summaryTotalEquipment">
				<tr>
					<td><div style="height:20px;"></div></td>
					<td><xsl:value-of select="@equipment"/></td>
					<td><div style="height:20px;"></div></td>
					<td><div style="height:20px;"></div></td>
					<td><div style="height:20px;"></div></td>
					<td><div style="height:20px;"></div></td> 					
					<td><xsl:value-of select="@qty"/></td>	
					<td><xsl:value-of select="@voc3"/></td>
					<td><div style="height:20px;"></div></td>
					<td><xsl:value-of select="@totalVoc"/></td>														
				</tr>
			</xsl:for-each>
			<!--sum-->
			<tr bgcolor="gray">
					<td><div style="height:20px;"></div></td>
					<td><div style="height:20px;"></div></td>
					<td><div style="height:20px;"><xsl:value-of select="rule"/> VOC TOTALS</div></td>
					<td><div style="height:20px;"></div></td>
					<td><div style="height:20px;"></div></td>
					<td><div style="height:20px;"></div></td> 					
					<td><xsl:value-of select="summary/summarySum/@qty"/></td>	
					<td><xsl:value-of select="summary/summarySum/@voc3"/></td>
					<td><div style="height:20px;"></div></td>
					<td><xsl:value-of select="summary/summarySum/@totalVoc"/></td>														
				</tr>
		</table>
	</xsl:template>
	
</xsl:stylesheet>

		