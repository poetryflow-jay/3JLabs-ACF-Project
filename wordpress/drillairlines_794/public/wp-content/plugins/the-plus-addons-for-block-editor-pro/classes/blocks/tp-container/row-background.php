<?php
/**
 * Row Background Rendering
 * Handles all background effects for TPGB Container
 * 
 * @package ThePlusAddons
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Render Row Background
 * 
 * @param array $attributes Block attributes
 * @param string $block_id Block ID
 */
function tpgb_render_row_background($attributes, $block_id) {
    $deepBgopt = (!empty($attributes['deepBgopt'])) ? $attributes['deepBgopt'] : '';
    $colorList = (!empty($attributes['colorList'])) ? $attributes['colorList'] : [];
    $animdur = (!empty($attributes['animdur'])) ? (int) $attributes['animdur'] : 3;
    $animDelay = (isset($attributes['animDelay'])) ? ($attributes['animDelay']) : 0;
    $imgeffect = (!empty($attributes['imgeffect'])) ? $attributes['imgeffect'] : '';
    $intensity = (!empty($attributes['intensity'])) ? $attributes['intensity'] : '';
    $Scale = (!empty($attributes['Scale'])) ? $attributes['Scale'] : '';
    $perspective = (!empty($attributes['perspective'])) ? $attributes['perspective'] : '';
    $invert = (!empty($attributes['inverted'])) ? 'true' : 'false';
    $scrollPara = (!empty($attributes['scrollPara'])) ? $attributes['scrollPara'] : false;
    $craBgeffect = (!empty($attributes['craBgeffect'])) ? $attributes['craBgeffect'] : 'columns_simple_image';
    $movedir = (!empty($attributes['movedir'])) ? $attributes['movedir'] : 'left';
    $trasispeed = (!empty($attributes['trasispeed'])) ? $attributes['trasispeed'] : '';
    $kburnseffect = (!empty($attributes['kburnseffect'])) ? $attributes['kburnseffect'] : false;
    $Kbeffctdir = (!empty($attributes['Kbeffctdir'])) ? $attributes['Kbeffctdir'] : '';
    $effctDure = (!empty($attributes['effctDure'])) ? $attributes['effctDure'] : '';
    $videosour = (!empty($attributes['videosour'])) ? $attributes['videosour'] : 'youtube';
    $youtubeId = (!empty($attributes['youtubeId'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['youtubeId']) : 'QrI0jo5JZSs';
    $videoImg = (!empty($attributes['videoImg'])) ? $attributes['videoImg'] : '';
    $vimeoId = (!empty($attributes['vimeoId'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['vimeoId']) : '';
    $rowImgs = (!empty($attributes['rowImgs'])) ? $attributes['rowImgs'] : [];
    $scrollchg = (!empty($attributes['scrollchg'])) ? $attributes['scrollchg'] : 'no';
    $midOption = (!empty($attributes['midOption'])) ? $attributes['midOption'] : '';
    $topOption = (!empty($attributes['topOption'])) ? $attributes['topOption'] : '';

    $dataAttr = $classname = $rowBgClass = $cssrule = $videoattr = $videoUrl = $midclass = $dataAttr2 = $midlayer = $loop = $midclass1 = '';
    $colors = array();
    $output = '';

    // Process Background Colors/Gradients/Scroll Effects
    if (!empty($deepBgopt) && ($deepBgopt == 'bg_color' || $deepBgopt == 'bg_animate_gradient' || $deepBgopt == 'scroll_animate_color')) {
        if (!empty($colorList)) {
            $i = 0;
            $scrolltra = (!empty($attributes['scrolltra'])) ? $attributes['scrolltra'] : '0.7';

            foreach ($colorList as $index => $item) {
                $actclass = '';
                if ($deepBgopt == 'bg_color' || $deepBgopt == 'bg_animate_gradient') {
                    $colors[] = $item['aniColor'];
                } else {
                    if (empty($item['scrollImg']['url'])) {
                        if (isset($item['aniBgtype']['openBg']) && !empty($item['aniBgtype']['openBg'])) {
                            if (isset($item['aniBgtype']['bgType']) && $item['aniBgtype']['bgType'] == 'color' && isset($item['aniBgtype']['bgDefaultColor']) && !empty($item['aniBgtype']['bgDefaultColor'])) {
                                $colors[] = $item['aniBgtype']['bgDefaultColor'];
                            } else {
                                if (isset($item['aniBgtype']['bgType']) && $item['aniBgtype']['bgType'] == 'gradient' && isset($item['aniBgtype']['bgGradient']) && !empty($item['aniBgtype']['bgGradient'])) {
                                    $colors[] = $item['aniBgtype']['bgGradient'];
                                }
                            }
                        }
                        if ($i == 0) {
                            $cssrule = 'background:' . esc_attr($item['aniColor']) . ';';
                            $actclass = ($scrollchg == 'no') ? 'active' : '';
                        }
                        if (!empty($scrollchg) && $scrollchg == 'no') {
                            $loop .= '<div class="tp-repeater-item-' . esc_attr($item['_key']) . ' tpgb-section-bg-scrolling ' . esc_attr($actclass) . '" style="background:' . esc_attr($item['aniColor']) . ';transition-duration: ' . esc_attr($scrolltra) . 's"></div>';
                        }
                    }
                    if (!empty($scrollchg) && $scrollchg == 'no' && !empty($item['scrollImg']) && !empty($item['scrollImg']['url'])) {
                        $colors[] = $item['scrollImg']['url'];
                        if ($i == 0) {
                            $cssrule = 'background:url(' . esc_url($item['scrollImg']['url']) . ');';
                            $actclass = ($scrollchg == 'no') ? 'active' : '';
                        }
                        if (!empty($scrollchg) && $scrollchg == 'no') {
                            $loop .= '<div class="tp-repeater-item-' . esc_attr($item['_key']) . ' tpgb-section-bg-scrolling ' . esc_attr($actclass) . '" style="background:url(' . esc_url($item['scrollImg']['url']) . ');transition-duration : ' . esc_attr($scrolltra) . 's"></div>';
                        }
                    }
                    if (isset($item['aniBgtype']) && !empty($item['aniBgtype']) && isset($item['aniBgtype']['openBg']) && $item['aniBgtype']['openBg'] == 1) {
                        $loop .= '<div class="tp-repeater-item-' . esc_attr($item['_key']) . ' tpgb-section-bg-scrolling ' . esc_attr($actclass) . '"></div>';
                    }
                }
                $i++;
            }
        }

        if ($deepBgopt == 'bg_color') {
            $dataAttr .= ' data-bg="' . htmlspecialchars(wp_json_encode($colors)) . '" data-bg-delay="' . esc_attr($animDelay) . '" data-bg-duration="' . esc_attr($animdur) . '" ';
            $classname = 'row-animat-bg';
        } else if ($deepBgopt == 'bg_animate_gradient') {
            $bgRotation = (!empty($attributes['bgRotation'])) ? $attributes['bgRotation'] : '';
            $bgduration = (!empty($attributes['bgduration'])) ? $attributes['bgduration'] : '';
            $fullBggra = (!empty($attributes['fullBggra'])) ? 'yes' : 'no';
            $bgposition = (!empty($attributes['bgposition'])) ? $attributes['bgposition'] : '';

            if ($colors != '') {
                $first = reset($colors);
                $last = end($colors);
                $grad_colors = implode(",", $colors);
            } else {
                $first = '#ff2d60';
                $last = '#1deab9';
                $grad_colors = '#ff2d60,#ff9132,#ff61fa,#6caafd,#29ccff,#1deab9';
            }
            $classname = "tpgb-row-bg-gradient";
            $cssrule = 'filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=' . esc_attr($first) . ', endColorstr=' . esc_attr($last) . ');background-image: linear-gradient(' . esc_attr($bgRotation) . 'deg,' . $grad_colors . ');animation-duration: ' . esc_attr($bgduration) . 's;';
            $dataAttr = 'data-full-page="' . esc_attr($fullBggra) . '" data-position="' . esc_attr($bgposition) . '"';
        } else if ($deepBgopt == 'scroll_animate_color') {
            if (!empty($scrollchg) && $scrollchg == 'yes') {
                $cssrule .= 'transition-duration:0s';
            }
            $classname = 'tpgb-row-scrollbg';
            $dataAttr = ' data-bgcolors="' . htmlspecialchars(wp_json_encode($colors)) . '" data-scrolling-effect=' . esc_attr($scrollchg) . ' ';
        }
    } else if (!empty($deepBgopt) && $deepBgopt == 'bg_image' && $craBgeffect == 'columns_simple_image' && $imgeffect == 'style-1') {
        $rowBgClass = ' tpgb-img-parallax-mouse';
        $classname = 'tpgb-img-parallax-hover';
        $dataAttr .= ' data-type="tilt" data-amount="' . esc_attr($intensity) . '" data-scale="' . esc_attr($Scale) . '" data-perspective="' . esc_attr($perspective) . '" data-inverted="' . esc_attr($invert) . '" data-opacity="1" ';
    } else if (!empty($deepBgopt) && $deepBgopt == 'bg_image' && $craBgeffect == 'columns_simple_image' && $imgeffect == 'style-2') {
        $rowBgClass = ' tpgb-img-parallax-mouse';
        $classname = 'tpgb-img-parallax-hover';
        $dataAttr .= ' data-type="move" data-amount="' . esc_attr($intensity) . '" data-scale="' . esc_attr($Scale) . '" data-inverted="' . esc_attr($invert) . '" data-opacity="1" ';
    } else if (!empty($deepBgopt) && $deepBgopt == 'bg_image' && $craBgeffect == 'columns_animated_bg') {
        $classname .= $craBgeffect;
        $classname .= ' row-bg-' . $movedir . '';
        $dataAttr .= ' data-direction="' . esc_attr($movedir) . '" data-trasition="' . esc_attr($trasispeed) . '" ';
    } else if (!empty($deepBgopt) && $deepBgopt == 'bg_video') {
        $classname = 'tpgb-video-' . esc_attr($videosour);
        if ($videosour == 'youtube') {
            $videoattr .= !empty($attributes['videoMute']) ? 'data-muted=1' : 'data-muted=0';
            $videoUrl .= !empty($attributes['videoMute']) ? '&amp;mute=1&amp;autoplay=1&amp;playlist=' . esc_attr($youtubeId) : '&amp;mute=0&amp;autoplay=1&amp;playlist=' . esc_attr($youtubeId);
            $videoUrl .= !empty($attributes['videoloop']) ? '&amp;loop=1' : '';
        }
        if ($videosour == 'vimeo') {
            $videoattr .= !empty($attributes['videoMute']) ? 'data-muted=1' : 'data-muted=0';
            $videoUrl .= !empty($attributes['videoloop']) ? '&amp;loop=1' : '';
        }
        if ($videosour == 'self-hosted') {
            $videoattr .= !empty($attributes['videoMute']) ? ' muted=true' : '';
            $videoattr .= !empty($attributes['videoloop']) ? ' loop=true' : '';
            if (!empty($attributes['mp4Url'])) {
                $mp4url = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['mp4Url']);
                $videoUrl .= ' src="' . esc_url($mp4url) . '"';
            }
            if (!empty($attributes['WebMUrl'])) {
                $weburl = Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_val($attributes['WebMUrl']);
                $videoUrl .= ' src="' . esc_url($weburl) . '"';
            }
        }
    } else if (!empty($deepBgopt) && $deepBgopt == 'bg_gallery') {
        $classname = 'row-img-slide';
    }
    
    if (!empty($kburnseffect)) {
        $cssrule .= '-webkit-animation: bg-kenburns-effect ' . esc_attr($Kbeffctdir) . 's cubic-bezier(0.445, 0.050, 0.550, 0.950) infinite ' . esc_attr($Kbeffctdir) . ' both;animation: bg-kenburns-effect ' . esc_attr($effctDure) . 's cubic-bezier(0.445, 0.050, 0.550, 0.950) infinite ' . esc_attr($Kbeffctdir) . ' both;';
    }

    // Process Middle Layer Effects
    if (!empty($midOption)) {
        if ($midOption == 'canvas') {
            $canvasSty = (!empty($attributes['canvasSty'])) ? $attributes['canvasSty'] : 'style-1';
            $patiColor = (!empty($attributes['patiColor'])) ? $attributes['patiColor'] : '';
            $canShape = (!empty($attributes['canShape'])) ? $attributes['canShape'] : '';
            $ctmJson = (!empty($attributes['ctmJson'])) ? $attributes['ctmJson'] : '';

            $midclass = 'canvas-' . $canvasSty . $block_id;
            if ($canvasSty == 'style-1' || $canvasSty == 'style-4' || $canvasSty == 'style-8') {
                $midclass1 .= ' canvas-' . esc_attr($canvasSty) . '';
            }

            $canvasarr = array();
            if ($canvasSty == 'style-1') {
                $particleList = (!empty($attributes['particleList'])) ? $attributes['particleList'] : [];
                if ($particleList != '') {
                    foreach ($particleList as $item) {
                        if (isset($item['bubbleColor']) && $item['bubbleColor'] != '') {
                            $canvasarr[] = $item['bubbleColor'];
                        } else {
                            $canvasarr = array('#dd3333', '#dd9933', '#eeee22', '#81d742', '#1e73be');
                        }
                    }
                }

                for ($ij = 1; $ij <= 50; $ij++) {
                    $size = wp_rand(1, 30) . 'px';
                    $midlayer .= '<div class="tpgb-bubble" style="height: ' . esc_attr($size) . ';width: ' . esc_attr($size) . ';animation-delay: -' . ($ij * 0.2) . 's;-webkit-transform:translate3d( ' . wp_rand(-2000, 2000) . 'px,  ' . wp_rand(-1000, 2000) . 'px, ' . wp_rand(-1000, 2000) . 'px);transform: translate3d( ' . wp_rand(-2000, 2000) . 'px,  ' . wp_rand(-1000, 2000) . 'px, ' . wp_rand(-1000, 2000) . 'px);background: ' . $canvasarr[array_rand($canvasarr)] . ';"></div>';
                }
            } else if ($canvasSty == 'style-2' || $canvasSty == 'style-3' || $canvasSty == 'style-4') {
                $dataAttr2 = 'data-color="' . esc_attr($patiColor) . '" ';
            } else if ($canvasSty == 'style-5' || $canvasSty == 'style-6' || $canvasSty == 'style-7') {
                $dataAttr2 = 'data-color="' . esc_attr($patiColor) . '" data-type="' . esc_attr($canShape) . '" ';
            } else if ($canvasSty == 'style-8') {
                $dataAttr2 = 'data-color="' . esc_attr($patiColor) . '" ';
                $midlayer .= '<canvas class="tpgb-snow-particles"></canvas>';
            } else {
                $dataAttr2 = 'data-patijson="' . esc_attr($ctmJson) . '" ';
            }
        } else if ($midOption == 'mordern_image_effect' || $midOption == 'moving_image') {
            $midimgList = (!empty($attributes['midimgList'])) ? $attributes['midimgList'] : [];
            $midclass = ($midOption == 'mordern_image_effect' ? 'tpgb-mordernimg-effect tpgb-mordern-parallax' : ($midOption == 'moving_image' ? 'tpgb-automove-img' : ''));
            $animCss = '';
            if (!empty($midimgList)) {
                foreach ($midimgList as $item) {
                    $scene_loop = $scrollAttr = [];
                    $imgsrc = $visibility = $imgcss = $magicAttr = '';
                    if (!empty($item['resVisib'])) {
                        $visibility .= (!empty($item['desHide']) ? 'desktop-hide ' : '');
                        $visibility .= (!empty($item['tabHide']) ? 'tablet-hide ' : '');
                        $visibility .= (!empty($item['moHide']) ? 'mobile-hide ' : '');
                    }
                    $Effectin = !empty($item['Effectin']) ? (int) $item['Effectin'] : 0;

                    if (!empty($item['parallaxImg']) && !empty($item['parallaxImg']['url'])) {
                        $imgsrc = $item['parallaxImg']['url'];
                    }
                    if ($midOption == 'moving_image' && !empty($imgsrc)) {
                        $imgcss .= 'background-image: url(' . esc_url($imgsrc) . ');background-size:' . (!empty($item['imgSize']) ? esc_attr($item['imgSize']) : '') . ' ';
                    }

                    if ($midOption == 'mordern_image_effect' && !empty($item['tpgbMagicScroll'])) {
                        $effect = ['vertical' => 'MSVertical', 'horizontal' => 'MSHorizontal', 'opacity' => 'MSOpacity', 'rotate' => 'MSRotate', 'scale' => 'MSScale', 'skew' => 'MSSkew', 'borderR' => 'borderR'];
                        foreach ($effect as $key => $val) {
                            if (isset($item[$val]) && !empty($item[$val]) && isset($item[$val]['tpgbReset']) && !empty($item[$val]['tpgbReset'])) {
                                unset($item[$val]['tpgbReset']);
                                $scene_loop[$key] = $item[$val];
                            }
                        }
                        if (isset($item['MSScrollOpt']) && !empty($item['MSScrollOpt']) && isset($item['MSScrollOpt']['tpgbReset']) && !empty($item['MSScrollOpt']['tpgbReset'])) {
                            $extraOpt = $item['MSScrollOpt'];
                            if (isset($extraOpt['trigger']) && !empty($extraOpt['trigger'])) {
                                $scene_loop['trigger'] = $extraOpt['trigger'];
                            }
                            if (isset($extraOpt['duration']) && !empty($extraOpt['duration'])) {
                                $scene_loop['duration'] = $extraOpt['duration'];
                            }
                            if (isset($extraOpt['offset']) && !empty($extraOpt['offset'])) {
                                $scene_loop['offset'] = $extraOpt['offset'];
                            }
                        } else {
                            $scene_loop['trigger'] = (object) ['md' => 0.5];
                            $scene_loop['duration'] = (object) ['md' => 300, "unit" => 'px'];
                            $scene_loop['offset'] = (object) ['md' => '0', "unit" => 'px'];
                        }

                        if (isset($item['MsadvOption']) && !empty($item['MsadvOption']) && isset($item['MsadvOption']['tpgbReset']) && !empty($item['MsadvOption']['tpgbReset'])) {
                            $advOpt = $item['MsadvOption'];

                            if (isset($advOpt['repeat']) && !empty($advOpt['repeat'])) {
                                $scene_loop['repeat'] = $advOpt['repeat'];
                            }
                            if (isset($advOpt['delay']) && !empty($advOpt['delay'])) {
                                $scene_loop['delay'] = $advOpt['delay'];
                            }
                            if (isset($advOpt['timing']) && !empty($advOpt['timing'])) {
                                $scene_loop['timing'] = $advOpt['timing'];
                            }
                            if (isset($advOpt['easing']) && !empty($advOpt['easing'])) {
                                $scene_loop['easing'] = $advOpt['easing'];
                            }
                            if (isset($advOpt['reverse'])) {
                                $scene_loop['reverse'] = $advOpt['reverse'];
                            }
                        } else {
                            $scene_loop['repeat'] = (object) ['md' => 0];
                            $scene_loop['delay'] = (object) ['md' => 0];
                            $scene_loop['timing'] = (object) ['md' => 1];
                            $scene_loop['reverse'] = true;
                        }

                        if (isset($item['MSSticky'])) {
                            $scene_loop['sticky'] = $item['MSSticky'];
                        }
                        if (isset($item['MSDevelop'])) {
                            $scene_loop['develop'] = $item['MSDevelop'];
                            if (isset($item['devName']) && !empty($item['devName'])) {
                                $scene_loop['develop_name'] = $item['devName'];
                            }
                        }
                        if (!empty($scene_loop)) {
                            $scrollAttr[] = $scene_loop;
                        }
                        $devices = ['md', 'sm', 'xs'];

                        $magicAttr = 'data-tpgb-ms="' . htmlspecialchars(wp_json_encode($scrollAttr), ENT_QUOTES, 'UTF-8') . '" data-tpgb-msview="' . htmlspecialchars(wp_json_encode($devices), ENT_QUOTES, 'UTF-8') . '" ';
                    }

                    // Set Transform origin
                    if (isset($item['modImgeff']) && !empty($item['modImgeff']) && $item['modImgeff'] == 'rotating' && !empty($item["tranOrigin"])) {
                        $animCss .= '-webkit-transform-origin: ' . esc_attr($item["tranOrigin"]) . ';-moz-transform-origin:' . esc_attr($item["tranOrigin"]) . ';-ms-transform-origin:' . esc_attr($item["tranOrigin"]) . ';-o-transform-origin:' . esc_attr($item["tranOrigin"]) . ';transform-origin:' . esc_attr($item["tranOrigin"]) . ';';
                    }
                    $midlayer .= '<div class="tpgb-parlximg-wrap tp-repeater-item-' . esc_attr($item['_key']) . ' ' . (($midOption == 'mordern_image_effect' ? 'tpgb-repet-img' : '')) . ' ' . esc_attr($visibility) . ' ' . ($midOption == 'mordern_image_effect' && !empty($item['tpgbMagicScroll']) ? ' tpgb_magic_scroll' : '') . '" style="' . esc_attr($imgcss) . '" data-direction="' . (!empty($item['imgDire']) ? esc_attr($item['imgDire']) : '') . '" data-trasition="' . esc_attr($Effectin) . '" ' . $magicAttr . '>';
                    if ($midOption == 'mordern_image_effect') {
                        if (!empty($item['tpgbMagicScroll'])) {
                            $midlayer .= '<div>';
                        }
                        $altText = (isset($item['parallaxImg']['alt']) && !empty($item['parallaxImg']['alt'])) ? esc_attr($item['parallaxImg']['alt']) : ((!empty($item['parallaxImg']['title'])) ? esc_attr($item['parallaxImg']['title']) : esc_attr__('Parallax Image', 'tpgbp'));

                        $midlayer .= '<img class="tpgb-parlximg ' . (isset($item['modImgeff']) && !empty($item['modImgeff']) && $midOption == 'mordern_image_effect' ? 'tpgb-imgeffect tpgb-' . $item['modImgeff'] : '') . '" src="' . esc_url($imgsrc) . '" alt="' . $altText . '" data-parallax="' . esc_attr($Effectin) . '" style="' . $animCss . '" />';
                        if (!empty($item['tpgbMagicScroll'])) {
                            $midlayer .= '</div>';
                        }
                    }
                    $midlayer .= '</div>';
                }
            }
        }
    }

    // Build Row Background Output
    $output .= '<div class="tpgb-row-background '.esc_attr($rowBgClass).' '.(!empty($attributes['parallax']) && $deepBgopt == 'bg_video'  ? ' fixed-bg-video' : '' ).'">';
			
        if(!empty($deepBgopt)){
            
            $deepStyleAttr = '';
            if(!empty($cssrule)){
                $deepStyleAttr = 'style="'.esc_attr($cssrule).'"';
            }

            $output .= '<div id="'.esc_attr($block_id).'" class="tpgb-deep-layer '.esc_attr($block_id).' '.esc_attr($classname).'" '.$dataAttr.' '.$deepStyleAttr.'>';

                if($deepBgopt == 'bg_video'){
                    $videoImgUrl = (isset($videoImg['dynamic'])) ? Tpgbp_Pro_Blocks_Helper::tpgb_dynamic_repeat_url($videoImg) : (!empty($videoImg['url']) ? $videoImg['url'] : '');

                    $vposterCss = '';
                    if(!empty($videoImgUrl)){
                        $vposterCss = 'style="background-image: url('.esc_url($videoImgUrl).');"';
                    }

                    $iframeTitle = (!empty($attributes['iframeTitle'])) ? esc_attr($attributes['iframeTitle']) : esc_attr__('My Video','tpgbp');
                    $output .= '<div class="video-poster-img video-tpgb-iframe-'.esc_attr($block_id).' '.(!empty($videoImg) && !empty($videoImg['url']) ? 'tp-loading' : '').'" '.( !empty($vposterCss) ? $vposterCss : '' ).'>';
                        $output .= '<div class="tpgb-video-wrap">';
                            if($videosour == 'youtube'){
                                $output .= '<iframe class="tpgb-iframe" id="tpgb-iframe-'.esc_attr($block_id).'" width="100%" height="100%" '.esc_attr($videoattr).' src="https://www.youtube.com/embed/'.esc_attr($youtubeId).'?wmode=opaque&amp;enablejsapi=1&amp;showinfo=0&amp;controls=0&amp;rel=0'.esc_attr($videoUrl).'" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" title="'.$iframeTitle.'"></iframe>';
                            }
                            if($videosour == 'vimeo'){
                                $output .= '<iframe class="tpgb-iframe" id="tpgb-iframe-' . esc_attr($block_id) . '" width="100%" height="100%" ' . esc_attr($videoattr) . ' src="https://player.vimeo.com/video/' . esc_attr($vimeoId) . '?api=1&autoplay=1&muted=1&portrait=0&controls=0' . esc_attr($videoUrl) . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="' . esc_attr($iframeTitle) . '"></iframe>';
                            }
                            if($videosour == 'self-hosted'){
                                $output .= '<video class="self-hosted-video" id="'.esc_attr($block_id).'"  data-autoplay="true" preload="auto" width="100%" height="100%" autoplay="true" playsinline '.esc_attr($videoattr).' data-setup="{}" '.$videoUrl.'></video>';
                            }
                        $output .= '</div>';
                    $output .= '</div>';
                
                }
                
                if($deepBgopt == 'bg_gallery'){
                    
                    $img_list = [];
                    $gallery_opt = [];
                    if(!empty($rowImgs)){
                        foreach($rowImgs as $img) {
                            if(!empty($img)) {
                                $img_list[] = array("src" => esc_url($img['url']) );
                            }
                        }		
                    }

                    $gallery_opt['transition'] = (!empty($attributes['transieffect'])) ? $attributes['transieffect'] : '';
                    $gallery_opt['transduration'] = (!empty($attributes['transdur'])) ? (int) $attributes['transdur'] : 2000;
                    $gallery_opt['duration'] = (!empty($attributes['slidetime'])) ? (int) $attributes['slidetime'] : 3000;
                    $gallery_opt['animation'] = (!empty($attributes['animation'])) ?  $attributes['animation'] : 'random';
                    $gallery_opt['textureoly'] = (!empty($attributes['textureoly']) && $attributes['textureoly'] != 'none' ) ? TPGB_URL.'assets/images/overlays/'.$attributes['textureoly'].'.png'  : '';
                    
                    $output .= '<div class="row-bg-slide" data-imgdata= \'' .wp_json_encode($img_list). '\' data-galleryopt=\'' .wp_json_encode($gallery_opt). '\'>';
                    $output .= '</div>';
                }
                $output .= $loop;
            $output .= '</div>';
        }
        
        if(!empty($midOption)){
        
            $output .= '<div id="'.($midOption == 'canvas' ? esc_attr($midclass) : '').'" class="tpgb-middle-layer '.esc_attr($midclass).esc_attr($midclass1).'" '.$dataAttr2.'>';
                $output .= $midlayer;
            $output .= '</div>';
            
        }
        
        if(!empty($topOption)){
            $output .= '<div class="tpgb-top-layer"></div>';
        }
        
    $output .= "</div>";
    $output = Tpgb_Blocks_Global_Options::block_Wrap_Render($attributes, $output);

    return $output;
}