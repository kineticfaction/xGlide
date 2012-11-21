<?xml version="1.0" encoding="UTF-8"?>

<!--
	Document   : default.xsl
	Author     : Oliver Ridgway
	Description: Displays some example data
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" version="1.0" encoding="utf-8" indent="no" standalone="no" omit-xml-declaration="yes"/>
	<xsl:template match="/nodes">
		<xsl:element name="ul">
			<xsl:for-each select="node">
				<xsl:element name="li">
					<xsl:value-of select="default_id"/>
					<xsl:text> : </xsl:text>
					<xsl:value-of select="default_name"/>
				</xsl:element>
			</xsl:for-each>
		</xsl:element>
	</xsl:template>
</xsl:stylesheet>