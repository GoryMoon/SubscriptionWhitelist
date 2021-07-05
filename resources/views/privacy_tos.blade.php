@extends('layout.base')

@section('title', ' - Privacy Policy & Terms of Service')

@section('content')
    <div class="privacy card-columns">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Privacy Policy</h2>
                <div class="card-text">
                    <p>This website isn't affiliated in any way with Twitch or any of it's affiliates</p>
                    <p>Your privacy is important to us. It is Subscriber Whitelist's policy to respect your privacy regarding any information we may collect from you across our website, <a href="{{ $home }}">{{ $home }}</a>, and other sites we own and operate.</p>
                    <p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used.</p>
                    <p>We only retain collected information for as long as you have an account with us. What data we store, we’ll protect within commercially acceptable means to prevent loss and theft, as well as unauthorised access, disclosure, copying, use or modification.</p>
                    <p>You can delete your information at anytime in your profile when logged in, doing so will remove your username/usernames from any whitelists.</p>
                    <p>We store:</p>
                    <ul>
                        <li>Twitch Id - To verify your subscriptions and identify you on login</li>
                        <li>Twitch Display Name - To show when you're logged in and To show your channels name when adding to list</li>
                        <li>Twitch Broadcaster Type - To determine if you are a broadcaster or not</li>
                        <li>Twitch AccessToken - Used to check subscription statuses</li>
                        <li>Zero or more provided usernames for the whitelists</li>
                        <li>If you names matches an MC name the uuid and name is stored</li>
                    </ul>
                    <p>We don’t share any personally identifying information publicly or with third-parties, except when required to by law.</p>
                    <p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies.</p>
                    <p>You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with your desired services.</p>
                    <p>To enrich and perfect your online experience, Subscriber Whitelist uses "Cookies", similar technologies and services provided by others to display personalized content, appropriate advertising and store your preferences on your computer.</p>
                    <p>A cookie is a string of information that a website stores on a visitor's computer, and that the visitor's browser provides to the website each time the visitor returns. Subscriber Whitelist uses cookies to help Subscriber Whitelist identify visitors and their usage of <a href="{{ $home }}">{{ $home }}</a>. Subscriber Whitelist visitors who do not wish to have cookies placed on their computers should set their browsers to refuse cookies before using Subscriber Whitelist's websites, with the drawback that any features of Subscriber Whitelist's websites will not function properly without the aid of cookies.</p>
                    <p>By continuing to navigate our website without changing your cookie settings, you hereby acknowledge and agree to Subscriber Whitelist's use of cookies.</p>
                    <p>Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information. If you have any questions about how we handle user data and personal information, feel free to contact us.</p>
                    <p>If you have any questions about this Privacy Policy, please contact us via <show-email-component></show-email-component></p>
                    <p>This policy is effective as of 5 July 2021.</p>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Subscriber Whitelist Terms of Service</h2>
                <div class="card-text">
                    <h3>1. Terms</h3>
                    <p>By accessing the website at <a href="{{ $home }}">{{ $home }}</a>, you are agreeing to be bound by these terms of service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this website are protected by applicable copyright and trademark law.</p>
                    <h3>2. Use License</h3>
                    <ol type="a">
                        <li>Permission is granted to temporarily download one copy of the materials (information or software) on Subscriber Whitelist's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
                            <ol type="i">
                                <li>modify or copy the materials;</li>
                                <li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li>
                                <li>remove any copyright or other proprietary notations from the materials; or</li>
                                <li>transfer the materials to another person or "mirror" the materials on any other server.</li>
                            </ol>
                        </li>
                        <li>This license shall automatically terminate if you violate any of these restrictions and may be terminated by Subscriber Whitelist at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.</li>
                    </ol>
                    <h3>3. Disclaimer</h3>
                    <ol type="a">
                        <li>The materials on Subscriber Whitelist's website are provided on an 'as is' basis. Subscriber Whitelist makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</li>
                        <li>Further, Subscriber Whitelist does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.</li>
                    </ol>
                    <h3>4. Limitations</h3>
                    <p>In no event shall Subscriber Whitelist or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Subscriber Whitelist's website, even if Subscriber Whitelist or a Subscriber Whitelist authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>
                    <h3>5. Accuracy of materials</h3>
                    <p>The materials appearing on Subscriber Whitelist's website could include technical or typographical errors. Subscriber Whitelist does not warrant that any of the materials on its website are accurate, complete or current. Subscriber Whitelist may make changes to the materials contained on its website at any time without notice. However Subscriber Whitelist does not make any commitment to update the materials.</p>
                    <h3>6. Links</h3>
                    <p>Subscriber Whitelist has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by Subscriber Whitelist of the site. Use of any such linked website is at the user's own risk.</p>
                    <h3>7. Modifications</h3>
                    <p>Subscriber Whitelist may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p>
                    <h3>8. Governing Law</h3>
                    <p>These terms and conditions are governed by and construed in accordance with the laws of Sweden and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>
                </div>
            </div>
        </div>
    </div>

@endsection
