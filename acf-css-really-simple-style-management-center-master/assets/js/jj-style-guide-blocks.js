/**
 * JJ Style Guide Blocks
 *
 * [Phase 10.2] Gutenberg Blocks
 * - Palette
 * - Typography
 * - Mini Guide
 *
 * @since 10.2.0
 */
( function ( wp ) {
    'use strict';

    if ( ! wp || ! wp.blocks ) {
        return;
    }

    var JJ = window.jjStyleGuideBlocks || {};

    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor ? wp.blockEditor.InspectorControls : null;
    var BlockControls = wp.blockEditor ? wp.blockEditor.BlockControls : null;
    var AlignmentToolbar = wp.blockEditor ? wp.blockEditor.AlignmentToolbar : null;
    var ToolbarGroup = wp.components ? wp.components.ToolbarGroup : null;
    var ToolbarButton = wp.components ? wp.components.ToolbarButton : null;
    var PanelBody = wp.components ? wp.components.PanelBody : null;
    var SelectControl = wp.components ? wp.components.SelectControl : null;
    var ToggleControl = wp.components ? wp.components.ToggleControl : null;
    var TextControl = wp.components ? wp.components.TextControl : null;
    var RangeControl = wp.components ? wp.components.RangeControl : null;
    var Notice = wp.components ? wp.components.Notice : null;
    var ServerSideRender = wp.serverSideRender;

    function t( key, fallback ) {
        if ( JJ && JJ.strings && JJ.strings[ key ] ) {
            return JJ.strings[ key ];
        }
        return fallback || key;
    }

    function getPaletteOptions() {
        if ( JJ && Array.isArray( JJ.palettes ) && JJ.palettes.length ) {
            return JJ.palettes;
        }
        return [
            { label: 'Brand', value: 'brand' },
            { label: 'System', value: 'system' },
        ];
    }

    function getTypographyOptions() {
        if ( JJ && Array.isArray( JJ.typography ) && JJ.typography.length ) {
            return JJ.typography;
        }
        return [
            { label: 'H1', value: 'h1' },
            { label: 'H2', value: 'h2' },
            { label: 'H3', value: 'h3' },
            { label: 'P', value: 'p' },
        ];
    }

    function ServerPreview( props ) {
        if ( ! ServerSideRender ) {
            return el( 'div', { className: 'jj-block-editor-fallback' }, t( 'preview_unavailable', '미리보기를 불러올 수 없습니다.' ) );
        }
        return el( ServerSideRender, {
            block: props.block,
            attributes: props.attributes,
            LoadingResponsePlaceholder: function () {
                return el( 'div', { className: 'jj-block-editor-loading' }, 'Loading…' );
            },
            EmptyResponsePlaceholder: function () {
                return el( 'div', { className: 'jj-block-editor-empty' }, '—' );
            },
            ErrorResponsePlaceholder: function () {
                return el( 'div', { className: 'jj-block-editor-error' }, 'Error' );
            },
        } );
    }

    // ===== Palette Block =====
    registerBlockType( 'jj-style-guide/palette', {
        apiVersion: 3,
        title: 'JJ ' + t( 'palette_label', '팔레트' ),
        icon: 'art',
        category: ( JJ && JJ.category ) ? JJ.category : 'design',
        description: 'JJ Style Center 팔레트를 블록으로 표시합니다.',
        keywords: [ 'palette', 'color', 'jj', 'style' ],
        attributes: {
            paletteKey: { type: 'string', default: 'brand' },
            showTitle: { type: 'boolean', default: true },
            showLabels: { type: 'boolean', default: true },
            showValues: { type: 'boolean', default: true },
            layout: { type: 'string', default: 'auto' },
            columns: { type: 'number', default: 0 },
            swatchHeight: { type: 'number', default: 54 },
        },
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var paletteOptions = getPaletteOptions();
            var hasPalette = !! ( paletteOptions && paletteOptions.length );

            var blockControls = null;
            if ( BlockControls && ToolbarGroup && ToolbarButton ) {
                blockControls = el(
                    BlockControls,
                    null,
                    el(
                        ToolbarGroup,
                        null,
                        el( ToolbarButton, {
                            icon: attributes.showLabels ? 'visibility' : 'hidden',
                            label: t( 'show_labels', '라벨 표시' ),
                            isPressed: !! attributes.showLabels,
                            onClick: function () {
                                setAttributes( { showLabels: ! attributes.showLabels } );
                            },
                        } )
                    )
                );
            }

            var controls = null;
            if ( InspectorControls && PanelBody && SelectControl && ToggleControl ) {
                controls = el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: t( 'panel_settings', '설정' ), initialOpen: true },
                        ( ! hasPalette && Notice )
                            ? el( Notice, { status: 'warning', isDismissible: false }, t( 'no_palette_available', '사용 가능한 팔레트가 없습니다.' ) )
                            : null,
                        el( SelectControl, {
                            label: t( 'palette_label', '팔레트' ),
                            value: attributes.paletteKey,
                            options: paletteOptions,
                            onChange: function ( value ) {
                                setAttributes( { paletteKey: value } );
                            },
                        } ),
                        el( ToggleControl, {
                            label: t( 'show_title', '제목 표시' ),
                            checked: !! attributes.showTitle,
                            onChange: function ( value ) {
                                setAttributes( { showTitle: !! value } );
                            },
                        } ),
                        el( ToggleControl, {
                            label: t( 'show_labels', '라벨 표시' ),
                            checked: !! attributes.showLabels,
                            onChange: function ( value ) {
                                setAttributes( { showLabels: !! value } );
                            },
                        } )
                    )
                );
            }

            var displayControls = null;
            if ( InspectorControls && PanelBody && SelectControl && RangeControl && ToggleControl ) {
                displayControls = el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: t( 'panel_display', '표시' ), initialOpen: false },
                        el( SelectControl, {
                            label: t( 'layout', '레이아웃' ),
                            value: attributes.layout,
                            options: [
                                { label: t( 'layout_auto', '자동' ), value: 'auto' },
                                { label: t( 'layout_compact', '컴팩트' ), value: 'compact' },
                            ],
                            onChange: function ( value ) {
                                setAttributes( { layout: value } );
                            },
                        } ),
                        el( ToggleControl, {
                            label: t( 'show_values', '값 표시' ),
                            checked: !! attributes.showValues,
                            onChange: function ( value ) { setAttributes( { showValues: !! value } ); },
                        } ),
                        el( RangeControl, {
                            label: t( 'columns', '컬럼' ),
                            value: attributes.columns || 0,
                            min: 0,
                            max: 8,
                            help: ( attributes.columns && attributes.columns > 0 ) ? ( attributes.columns + ' cols' ) : t( 'layout_auto', '자동' ),
                            onChange: function ( value ) { setAttributes( { columns: value } ); },
                        } ),
                        el( RangeControl, {
                            label: t( 'swatch_height', '스와치 높이(px)' ),
                            value: attributes.swatchHeight || 54,
                            min: 16,
                            max: 120,
                            onChange: function ( value ) { setAttributes( { swatchHeight: value } ); },
                        } )
                    )
                );
            }

            return el(
                'div',
                { className: props.className },
                blockControls,
                controls,
                displayControls,
                el( ServerPreview, { block: 'jj-style-guide/palette', attributes: attributes } )
            );
        },
        save: function () {
            return null; // dynamic
        },
    } );

    // ===== Typography Block =====
    registerBlockType( 'jj-style-guide/typography', {
        apiVersion: 3,
        title: 'JJ ' + t( 'include_typography', '타이포그래피' ),
        icon: 'editor-textcolor',
        category: ( JJ && JJ.category ) ? JJ.category : 'design',
        description: 'JJ 타이포그래피 설정을 미리보기로 표시합니다.',
        keywords: [ 'typography', 'font', 'jj', 'style' ],
        attributes: {
            tag: { type: 'string', default: 'h2' },
            text: { type: 'string', default: '타이포그래피 미리보기' },
            align: { type: 'string', default: '' },
        },
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var typoOptions = getTypographyOptions();
            var hasTypo = !! ( typoOptions && typoOptions.length );

            var blockControls = null;
            if ( BlockControls && AlignmentToolbar ) {
                blockControls = el(
                    BlockControls,
                    null,
                    el( AlignmentToolbar, {
                        value: attributes.align,
                        onChange: function ( next ) {
                            setAttributes( { align: next || '' } );
                        },
                    } )
                );
            }

            var controls = null;
            if ( InspectorControls && PanelBody && SelectControl && TextControl ) {
                controls = el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: t( 'panel_content', '콘텐츠' ), initialOpen: true },
                        ( ! hasTypo && Notice )
                            ? el( Notice, { status: 'warning', isDismissible: false }, t( 'no_typography_available', '사용 가능한 타이포그래피가 없습니다.' ) )
                            : null,
                        el( SelectControl, {
                            label: t( 'typography_key', '스타일 키' ),
                            value: attributes.tag,
                            options: typoOptions,
                            onChange: function ( value ) {
                                setAttributes( { tag: value } );
                            },
                        } ),
                        el( TextControl, {
                            label: t( 'text', '텍스트' ),
                            value: attributes.text,
                            onChange: function ( value ) {
                                setAttributes( { text: value } );
                            },
                        } )
                    )
                );
            }

            return el(
                'div',
                { className: props.className },
                blockControls,
                controls,
                el( ServerPreview, { block: 'jj-style-guide/typography', attributes: attributes } )
            );
        },
        save: function () {
            return null; // dynamic
        },
    } );

    // ===== Mini Guide Block =====
    registerBlockType( 'jj-style-guide/mini-guide', {
        apiVersion: 3,
        title: 'JJ ' + t( 'include_buttons', '미니 스타일 가이드' ),
        icon: 'admin-customizer',
        category: ( JJ && JJ.category ) ? JJ.category : 'design',
        description: '팔레트/타이포/버튼을 한 번에 표시합니다.',
        keywords: [ 'styleguide', 'jj', 'tokens' ],
        attributes: {
            includePalettes: { type: 'boolean', default: true },
            includeTypography: { type: 'boolean', default: true },
            includeButtons: { type: 'boolean', default: true },
            paletteKey: { type: 'string', default: 'brand' },
            compact: { type: 'boolean', default: false },
        },
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var controls = null;
            if ( InspectorControls && PanelBody && SelectControl && ToggleControl ) {
                controls = el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: t( 'panel_display', '표시' ), initialOpen: true },
                        el( ToggleControl, {
                            label: t( 'include_palettes', '팔레트' ),
                            checked: !! attributes.includePalettes,
                            onChange: function ( value ) { setAttributes( { includePalettes: !! value } ); },
                        } ),
                        el( ToggleControl, {
                            label: t( 'include_typography', '타이포그래피' ),
                            checked: !! attributes.includeTypography,
                            onChange: function ( value ) { setAttributes( { includeTypography: !! value } ); },
                        } ),
                        el( ToggleControl, {
                            label: t( 'include_buttons', '버튼' ),
                            checked: !! attributes.includeButtons,
                            onChange: function ( value ) { setAttributes( { includeButtons: !! value } ); },
                        } ),
                        el( SelectControl, {
                            label: t( 'palette_for_mini_guide', '팔레트(미니 가이드용)' ),
                            value: attributes.paletteKey,
                            options: getPaletteOptions(),
                            onChange: function ( value ) { setAttributes( { paletteKey: value } ); },
                        } ),
                        el( ToggleControl, {
                            label: t( 'layout_compact', '컴팩트' ),
                            checked: !! attributes.compact,
                            onChange: function ( value ) { setAttributes( { compact: !! value } ); },
                        } )
                    )
                );
            }

            return el(
                'div',
                { className: props.className },
                controls,
                el( ServerPreview, { block: 'jj-style-guide/mini-guide', attributes: attributes } )
            );
        },
        save: function () {
            return null; // dynamic
        },
    } );

    // ===== Live Page Block (v13.4.2) =====
    registerBlockType( 'jj-style-guide/live-page', {
        apiVersion: 3,
        title: 'JJ 스타일 가이드 라이브',
        icon: 'book-alt',
        category: ( JJ && JJ.category ) ? JJ.category : 'design',
        description: '전체 스타일 가이드 라이브 페이지를 표시합니다. (숏코드 [jj_style_guide_live] 대체)',
        keywords: [ 'styleguide', 'live', 'jj', 'style', 'center' ],
        attributes: {},
        example: {},
        edit: function ( props ) {
            return el(
                'div',
                { className: props.className },
                el( ServerPreview, { block: 'jj-style-guide/live-page', attributes: {} } )
            );
        },
        save: function () {
            return null; // dynamic server-side render
        },
    } );
} )( window.wp );

