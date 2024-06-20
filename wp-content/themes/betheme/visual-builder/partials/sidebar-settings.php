<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

//$post_id = intval( $_GET['post'] );

if( $this->template_type && in_array( $this->template_type, array('header', 'footer', 'megamenu', 'popup') ) ){
	$this->options['builder-blocks-disabled'] = true;
}

echo '<div class="panel panel-settings" style="display: none;">
	<div class="mfn-form">';

	echo '<ul class="settings-links">
        <li>
            <a href="#" class="shortcutsinfo-open">
                <span class="mfn-icon mfn-icon-shortcuts"></span>
                <p>Keyboard shortcuts</p>
            </a>
        </li>
        <li>
            <a href="#" class="dynamicdatainfo-open">
                <span class="mfn-icon mfn-icon-dynamic-data"></span>
                <p>Dynamic data</p>
            </a>
        </li>
    </ul>';

	if( ! empty($this->options['builder-blocks-disabled']) || empty($this->options['builder-blocks']) ){

		echo '<div class="mfn-form-row mfn-row">
		  <div class="row-column row-column-12">
		    <div class="form-content form-content-full-width">
		      <div class="form-group segmented-options single-segmented-option settings">

		        <span class="mfn-icon mfn-icon-navigation"></span>

		        <div class="setting-label">
		          <h5>Navigation</h5>
		        </div>

		        <div class="form-control" data-option="mfn-modern-nav">
		          <ul>
		            <li class="active" data-value="1"><a href="#"><span class="text">Modern</span></a></li>
		            <li data-value="0"><a href="#"><span class="text">Classic</span></a></li>
		          </ul>
		        </div>

		      </div>
		    </div>
		  </div>
		</div>';

	}

	echo '<div class="mfn-form-row mfn-row mfn-reload-required">
	  <div class="row-column row-column-12">
	    <div class="form-content form-content-full-width">
	      <div class="form-group segmented-options single-segmented-option settings">

	        <span class="mfn-icon mfn-icon-column"></span>

	        <div class="setting-label">
	          <h5>Column text editor</h5>
	          <p>CodeMirror or TinyMCE</p>
	        </div>

	        <div class="form-control" data-option="column-visual">
	          <ul>
	            <li class="active" data-value="0"><a href="#"><span class="text">Code</span></a></li>
	            <li data-value="1"><a href="#"><span class="text">Visual</span></a></li>
	          </ul>
	        </div>

	      </div>
	    </div>
	  </div>
	</div>';

	// BeBuilder Blocks

	if( empty($this->options['builder-blocks-disabled']) ){

		echo '<div class="mfn-form-row mfn-row mfn-reload-required">
		  <div class="row-column row-column-12">
		    <div class="form-content form-content-full-width">
		      <div class="form-group segmented-options single-segmented-option settings">

		        <span class="mfn-icon mfn-icon-builder-mode"></span>

		        <div class="setting-label">
		          <h5>Builder Mode</h5>
		          <p>Classic blocks builder or Live builder</p>
		        </div>

		        <div class="form-control" data-option="builder-blocks">
		          <ul>
		            <li data-value="1"><a href="#"><span class="text">Blocks</span></a></li>
								<li class="active" data-value="0"><a href="#"><span class="text">Live</span></a></li>
		          </ul>
		        </div>

		      </div>
		    </div>
		  </div>
		</div>';

	}

	if( empty($this->options['builder-blocks-disabled']) && ! empty($this->options['builder-blocks']) ){

		echo '<div class="mfn-form-row mfn-row">
		  <div class="row-column row-column-12">
		    <div class="form-content form-content-full-width">
		      <div class="form-group segmented-options single-segmented-option settings">

		        <span class="mfn-icon mfn-icon-simple-view"></span>

		        <div class="setting-label">
		          <h5>Simple view</h5>
		          <p>Simplified version of elements</p>
		        </div>

		        <div class="form-control" data-option="simple-view">
		          <ul>
						<li class="active" data-value="0"><a href="#"><span class="text">Off</span></a></li>
						<li data-value="1"><a href="#"><span class="text">On</span></a></li>
		          </ul>
		        </div>

		      </div>
		    </div>
		  </div>
		</div>';

		echo '<div class="mfn-form-row mfn-row">
		  <div class="row-column row-column-12">
		    <div class="form-content form-content-full-width">
		      <div class="form-group segmented-options single-segmented-option settings">

		        <span class="mfn-icon mfn-icon-hover-effects"></span>

		        <div class="setting-label">
		          <h5>Hover effects</h5>
		          <p>Builder element bar shows on hover</p>
		        </div>

		        <div class="form-control" data-option="hover-effects">
		          <ul>
								<li data-value="1"><a href="#"><span class="text">Off</span></a></li>
								<li class="active" data-value="0"><a href="#"><span class="text">On</span></a></li>
		          </ul>
		        </div>

		      </div>
		    </div>
		  </div>
		</div>';

	}

	echo '<div class="mfn-form-row mfn-row">
	  <div class="row-column row-column-12">
	    <div class="form-content form-content-full-width">
	      <div class="form-group segmented-options single-segmented-option settings">

	        <span class="mfn-icon mfn-icon-navigator-position"></span>

	        <div class="setting-label">
	          <h5>Navigator</h5>
	        </div>

	        <div class="form-control" data-option="navigator-position">
	          <ul>
	            <li class="active" data-value="0"><a href="#"><span class="text">Default</span></a></li>
	            <li data-value="1"><a href="#"><span class="text">Side</span></a></li>
	          </ul>
	        </div>

	      </div>
	    </div>
	  </div>
	</div>';

	echo '<div class="mfn-form-row mfn-row">
	  <div class="row-column row-column-12">
	    <div class="form-content form-content-full-width">
	      <div class="form-group segmented-options single-segmented-option settings">

	        <span class="mfn-icon mfn-icon-history-mode"></span>

	        <div class="setting-label">
	          <h5>History mode</h5>
	          <p>Ajax is slower but has more storage</p>
	        </div>

	        <div class="form-control" data-option="history-mode">
	          <ul>
	            <li class="active" data-value="0"><a href="#"><span class="text">Default</span></a></li>
	            <li data-value="1"><a href="#"><span class="text">Ajax</span></a></li>
	          </ul>
	        </div>

	      </div>
	    </div>
	  </div>
	</div>';

	// UI mode

	echo '<div class="mfn-form-row mfn-row mfn-reload-required">
	  <div class="row-column row-column-12">
	    <div class="form-content form-content-full-width">
	      <div class="form-group segmented-options single-segmented-option settings">

	        <span class="mfn-icon mfn-icon-user-interface"></span>

	        <div class="setting-label">
	          <h5>User interface</h5>
	        </div>

	        <div class="form-control" data-option="user-interface">
	          <ul>
	            <li class="active" data-value="default"><a href="#"><span class="text">Default</span></a></li>
	            <li data-value="dev"><a href="#"><span class="text">Developer</span></a></li>
	          </ul>
	        </div>

	      </div>
	    </div>
	  </div>
	</div>';

	echo '<div class="mfn-form-row mfn-row">
	  <div class="row-column row-column-12">
	    <div class="form-content form-content-full-width">
	      <div class="form-group segmented-options single-segmented-option settings">

	        <span class="mfn-icon mfn-icon-scalable-preview"></span>

	        <div class="setting-label">
	          <h5>Scalable Preview</h5>
	          <p>Adjust preview to your screen</p>
	        </div>

	        <div class="form-control" data-option="scalable-preview">
	          <ul>
	            <li class="active" data-value=""><a href="#"><span class="text">Disable</span></a></li>
	            <li data-value="enable"><a href="#"><span class="text">Enable</span></a></li>
	          </ul>
	        </div>

	      </div>
	    </div>
	  </div>
	</div>';

	echo '<div class="mfn-form-row mfn-row">
	  <div class="row-column row-column-12">
	    <div class="form-content form-content-full-width">
	      <div class="form-group segmented-options single-segmented-option settings">

	        <span class="mfn-icon mfn-icon-dark-mode"></span>

	        <div class="setting-label">
	          <h5>Color scheme</h5>
	        </div>

	        <div class="form-control" data-option="ui-theme">
	          <ul>
	            <li class="active" data-value="mfn-ui-auto"><a href="#"><span class="text">Auto</span></a></li>
	            <li data-value="mfn-ui-light"><a href="#"><span class="text">Light</span></a></li>
	            <li data-value="mfn-ui-dark"><a href="#"><span class="text">Dark</span></a></li>
	          </ul>
	        </div>

	      </div>
	    </div>
	  </div>
	</div>';

echo '</div>
</div>';
