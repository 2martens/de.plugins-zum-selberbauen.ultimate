<div id="setupTab" class="tabMenuContainer tabMenuContent containerPadding panel" data-store="setupTab-grid">
	<nav class="tabMenu subTabsContainer">
		<ul class="subTabs">
			<li><a href="#setupTab-grid" title="{lang}ultimate.visualEditor.setupTab.grid{/lang}">{lang}ultimate.visualEditor.setupTab.grid{/lang}</a></li>
			<li><a href="#setupTab-wrapper" title="{lang}ultimate.visualEditor.setupTab.wrapper{/lang}">{lang}ultimate.visualEditor.setupTab.wrapper{/lang}</a></li>
		</ul>
	</nav>
	
	<div class="subTabsContentContainer">
		<div id="setupTab-grid" class="tabMenuContent subTabsContent containerPadding">
		<div class="info">
			<p>{lang}ultimate.visualEditor.setupTab.grid.info{/lang}</p>
		</div>
		<dl class="wide">
			<dd>
				<div>
					<label for="columnWidthInput" class="jsTooltip" title="{lang}ultimate.visualEditor.setupTab.grid.columnWidth.description{/lang}">{lang}ultimate.visualEditor.setupTab.grid.columnWidth{/lang}</label>
					<div id="columnWidthSliderBar" class="medium sliderBar jsTooltip" title="{lang}ultimate.visualEditor.setupTab.grid.columnWidth.description{/lang}"></div>
					
					<div id="columnWidthSliderBarText" class="sliderBarText">
						<input readonly="readonly" type="text" id="columnWidthInput" name="columnWidth" value="{$columnWidth}" /><span class="sliderUnit">px</span>
					</div>
				</div>
			</dd>
			<dd>
				<div>
					<label for="gutterWidthInput" class="jsTooltip" title="{lang}ultimate.visualEditor.setupTab.grid.gutterWidth.description{/lang}">{lang}ultimate.visualEditor.setupTab.grid.gutterWidth{/lang}</label>
					<div id="gutterWidthSliderBar" class="medium sliderBar jsTooltip" title="{lang}ultimate.visualEditor.setupTab.grid.gutterWidth.description{/lang}"></div>
					
					<div id="gutterWidthSliderBarText" class="sliderBarText">
						<input readonly="readonly" type="text" id="gutterWidthInput" name="gutterWidth" value="{$gutterWidth}" class="sliderValue" /><span class="sliderUnit">px</span>
					</div>
				</div>
			</dd>
			<dd>
				<div>
					<label for="gridWidth" class="jsTooltip" title="{lang}ultimate.visualEditor.setupTab.grid.gridWidth.description{/lang}">{lang}ultimate.visualEditor.setupTab.grid.gridWidth{/lang}</label>
					<div>
						<input type="text" readonly="readonly" name="gridWidth" id="gridWidthInput" value="{$gridWidth}" /><span class="suffix">px</span>
					</div>
				</div>
			</dd>
		</dl>
		</div>
		<div id="setupTab-wrapper" class="tabMenuContent subTabsContent containerPadding">
			<div class="info">
				<p>{lang}ultimate.visualEditor.setupTab.wrapper.info{/lang}</p>
			</div>
			<dl class="wide">
				<dd>
					<div>
						<label for="wrapperTopMarginInput" class="jsTooltip" title="{lang}ultimate.visualEditor.setupTab.wrapper.wrapperTopMargin.description{/lang}">{lang}ultimate.visualEditor.setupTab.wrapper.wrapperTopMargin{/lang}</label>
						<div id="wrapperTopMarginSliderBar" class="medium sliderBar jsTooltip" title="{lang}ultimate.visualEditor.setupTab.wrapper.wrapperTopMargin.description{/lang}"></div>
					
						<div id="wrapperTopMarginSliderBarText" class="sliderBarText">
							<input readonly="readonly" type="text" id="wrapperTopMarginInput" name="wrapperTopMargin" value="{$wrapperTopMargin}" /><span class="sliderUnit">px</span>
						</div>
					</div>
				</dd>
				<dd>
					<div>
						<label for="wrapperBottomMarginInput" class="jsTooltip" title="{lang}ultimate.visualEditor.setupTab.wrapper.wrapperBottomMargin.description{/lang}">{lang}ultimate.visualEditor.setupTab.wrapper.wrapperBottomMargin{/lang}</label>
						<div id="wrapperBottomMarginSliderBar" class="medium sliderBar jsTooltip" title="{lang}ultimate.visualEditor.setupTab.wrapper.wrapperBottomMargin.description{/lang}"></div>
					
						<div id="wrapperBottomMarginSliderBarText" class="sliderBarText">
							<input readonly="readonly" type="text" id="wrapperBottomMarginInput" name="wrapperBottomMargin" value="{$wrapperBottomMargin}" /><span class="sliderUnit">px</span>
						</div>
					</div>
				</dd>
			</dl>
		</div>
	</div>
	<script type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		$('#columnWidthSliderBar').slider({
			min: 10,
			range: 'min',
			max: 80,
			step: 1,
			value: {@$columnWidth},
			slide: function(event, ui) {
				$('#columnWidthInput').val(ui.value);
			}
		});
		$('#columnWidthInput').val( $('#columnWidthSliderBar').slider('value') );
		$('#gutterWidthSliderBar').slider({
			range: 'min',
			min: 0,
			max: 30,
			step: 1,
			value: {@$gutterWidth},
			slide: function(event, ui) {
				$('#gutterWidthInput').val(ui.value);
			}
		});
		$('#gutterWidthInput').val( $('#gutterWidthSliderBar').slider('value') );
		
		$('#wrapperTopMarginSliderBar').slider({
			min: 0,
			range: 'min',
			max: 100,
			step: 5,
			value: {@$wrapperTopMargin},
			slide: function(event, ui) {
				$('#wrapperTopMarginInput').val(ui.value);
			}
		});
		$('#wrapperTopMarginInput').val( $('#wrapperTopMarginSliderBar').slider('value') );
		$('#wrapperBottomMarginSliderBar').slider({
			min: 0,
			range: 'min',
			max: 100,
			step: 5,
			value: {@$wrapperBottomMargin},
			slide: function(event, ui) {
				$('#wrapperBottomMarginInput').val(ui.value);
			}
		});
		$('#wrapperBottomMarginInput').val( $('#wrapperBottomMarginSliderBar').slider('value') );
	});
	/* ]]> */
	</script>
</div>