<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>
	
	<xsl:template match="page">
		<center><h1> <xsl:value-of select="title"/> </h1></center>
		
		<xsl:apply-templates select="company"/>
		<xsl:apply-templates select="facility"/>
		<xsl:apply-templates select="department"/>
		
		<xsl:apply-templates select="equipments"/>
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
	
	<xsl:template match="equipments">
		<table border="1">
			<tr bgcolor="gray">
				<th> Equipment </th>
				<th> Name </th>
				<th> CAS </th>
				<th> VOC/PM </th>
				<th> LOW </th>
				<th> HIGH </th>
				<th> AVG </th>
				<th> TOTAL (LBS) </th>
				<th> AVG/HOUR </th>
				<th> CA-AB 2588 </th>
				<th> SARA 313 </th>
			</tr>
			
			<xsl:for-each select="equipment">
				<tr>
					<td> <xsl:value-of select="@description"/> </td>
					
					<!-- Name -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="compoundName"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					<!-- CAS -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="cas"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					<!-- VOC/PM -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="vocpm"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					<!-- LOW -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="low"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					<!-- HIGH -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="high"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					
					<!-- AVG -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="avg"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					
					<!-- TOTAL (LBS) -->
					<td>
							<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="total"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					
					<!-- AVG/HOUR -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td> <xsl:value-of select="avgHour"/> </td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					<!-- CA-AB 2588 -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td>
									<xsl:choose>
										<xsl:when test="caab2588 = 'N/A'">
        								</xsl:when>
										
										<xsl:otherwise>
		          							<xsl:value-of select="caab2588"/>
				        				</xsl:otherwise>
			        				</xsl:choose>
								</td></tr>
							</xsl:for-each>
						</table>
					</td>
					
					<!-- SARA 313 -->
					<td>
						<table>
							<xsl:for-each select="compound">
								<tr><td>
									<xsl:choose>
										<xsl:when test="sara313 = 'N/A'">
        								</xsl:when>
										
										<xsl:otherwise>
		          							<xsl:value-of select="sara313"/>
				        				</xsl:otherwise>
			        				</xsl:choose>      		
								</td></tr>
							</xsl:for-each>
						</table>
					</td>
					
				</tr>
			</xsl:for-each>
			
		</table>
	</xsl:template>
	
</xsl:stylesheet>